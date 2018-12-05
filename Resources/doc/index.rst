Chameleon System SanityCheck
============================

SanityCheck adds diagnosis capabilities to any PHP application. By defining a range of checks, it can be ensured that
requirements for running the application are met continuously. For example, available disk space, granted file
permissions or availability of remote systems can be checked.

When and where these checks are performed is up to you. In an installer, from the command line or an admin
interface - just add the checks wherever they suit your needs.

Installation
------------

Step 1: Download the Library
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code-block:: bash

    $ composer require chameleon-system/sanitycheck

This command requires you to have Composer installed globally, as explained
in the `installation chapter`_ of the Composer documentation.

If you use the Symfony framework, it is recommended to use the SanityCheckBundle instead, as it integrates with
the Symfony container and eases configuration and usage.


Step 2: Define Checks
~~~~~~~~~~~~~~~~~~~~~

Define checks as described below.

Step 3: Perform Checks
~~~~~~~~~~~~~~~~~~~~~~

Run the defined checks as described below.

Usage
-----

Defining Checks
~~~~~~~~~~~~~~~

Let's look at an example:

.. code-block:: php

    $check = new PhpRuntimeVersionCheck(CheckOutcome::ERROR, '7.1.3');

This defines a check that tests if the system's PHP version is at least 7.1.3. If this requirement is not fulfilled,
an error on level ERROR will be raised.

While one may argue that the required PHP version should better be enforced by composer, the project code might be
deployed on another system where composer is not available (or the project might not use composer at all). After all,
this is only an example and there are far more checks to use, and custom check types can be created.


Executing Checks
~~~~~~~~~~~~~~~~

Checks are executed by the CheckHandler, e.g.:

.. code-block:: php

    $check = new PhpRuntimeVersionCheck(CheckOutcome::ERROR, '7.1.3');

    $checkResolver = new CheckResolver();
    $checkResolver->addCheck('my-check', $check);

    $checkHandler = new CheckHandler($checkResolver);
    $outcomeList = $checkHandler->checkAll();

The $outcomeList will then contain a list of CheckOutcome objects. These objects basically contain an outcome
level and a message key. The message key is meant to refer to the key in the Symfony translator, so that the message
can be printed in a localized way. Of course you can simply use plain messages in custom checks.


Printing Outcome Data
~~~~~~~~~~~~~~~~~~~~~

After you have retrieved the outcome data, use an implementation of `ChameleonSystem\\SanityCheck\\Output\\CheckOutputInterface`
to print the list.

.. code-block:: php

    $output = new DefaultCheckOutput();
    foreach ($outcomeList as $outcome) {
        $output->gather($outcome);
    }
    $output->commit();

As you can see, the output is divided into 2 phases: First we gather all output information, then we commit the
collected messages. Thus it is possible to e.g. send an email only once at the end. To simplify this process, an output
implementation may choose to handle the complete output in the gather() method. As the DefaultCheckOutput does this, we
could omit the last line in the example.


Creating Custom Check Classes
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To create a custom check class, either

a) create a new Check class that implements `ChameleonSystem\\SanityCheck\\Check\\CheckInterface`

or

b) create a new Check class that extends `ChameleonSystem\\SanityCheck\\Check\\AbstractCheck` (recommended).

The `CheckInterface` interface requires to implement two methods: `check()` and `getLevel()`. The `check` method does
the actual work and must return an array of `ChameleonSystem\\SanityCheck\\Outcome\\CheckOutcome` objects (the array may
contain one or more outcomes). The `getLevel()` method simply returns the configured level of the check.

The `AbstractCheck` class contains only a few lines of boilerplate code to initialize and return the check level.


Output Formatters
~~~~~~~~~~~~~~~~~

An output formatter adds bells and whistles to outcome messages. This might be some HTML code or console formatting.

Normally you won't need to deal with these formatters - the default outputs will use the appropriate formatter for HTML
or console output (which are the predefined formatters).

Check Suites
~~~~~~~~~~~~

Check suites are used to bundle some checks and outputs, so that only a single line of code is needed to execute and
output checks.

Because of the quite complex constructor it is best used in a framework. The SanityCheckBundle defines an abstract check
suite that limits the setup to the check data itself in common cases.

Example:

.. code-block:: php

    $check = new PhpRuntimeVersionCheck(CheckOutcome::ERROR, '7.1.3');

    $checkResolver = new CheckResolver();
    $checkResolver->addCheck('my-check', $check);

    $checkHandler = new CheckHandler($checkResolver);

    $outputResolver = new OutputResolver();
    $outputResolver->addOutput('default', new DefaultCheckOutput());
    $output = new DefaultCheckOutput();

    $checks = array('my-check');

    $suite = new CheckSuite(
        $checkHandler,
        $outputResolver,
        CheckOutcome::OK,
        $output,
        $checks
    );
    $suite->execute();


Predefined Checks
-----------------

DiskSpaceCheck
~~~~~~~~~~~~~~

Checks if a certain amount of disk space is available.

Configuration:

- check level
- directory (the disk on which this directory is located will be checked)
- thresholds

The thresholds parameter is an array of single threshold parameters. Each of these parameters consists of:

- value: the amount of space that needs to be available
- key: the check level to raise if the available disk space is below the given value

The value parameter needs to be in one of these formats:

- a numeric value of bytes
- a numeric value followed by one of ('B', 'KiB', 'MiB', 'GiB', 'TiB')
- a percentage value

Examples:

To raise a warning if below 1GiB and an error if below 100MiB use something like this:

.. code-block:: php

    $check = new DiskSpaceCheck(
        CheckOutcome::ERROR,
        '/path/to/check',
        array(
            CheckOutcome::WARNING => '1GiB',
            CheckOutcome::ERROR => '100MiB',
        ),
    );

The '/path/to/check' path defines the data storage that should be checked. As disks are mounted into arbitrary mount
points in the file system, it is required to specify any directory that is physically located on the correct disk.

To raise a warning if below 5% use something like this:

.. code-block:: php

    $check = new DiskSpaceCheck(
        CheckOutcome::ERROR,
        '/path/to/check',
        array(
            CheckOutcome::WARNING => '5%',
        ),
    );

ExpressionCheck
~~~~~~~~~~~~~~~

Checks if a given expression returns true. There are two caveats when using this check:

- It uses the PHP `eval` function without further checks, so be careful which expressions you use.
- A quite cryptic message is given if the check fails - a non-technical user will most likely find it difficult to
  understand.

Configuration:

- check level
- an array of expression strings


FileExistsCheck
~~~~~~~~~~~~~~~

Checks if a file or directory exists.

Configuration:

- check level
- an array of files or directories to check for
- base directory (optional) - if provided, all files/directories from the array parameter will be expected relative to
  this directory.

Examples:

To check if cache/ and logs/ exist in the current directory use something like this:

.. code-block:: php

    $check = new FileExistsCheck(
        CheckOutcome::ERROR,
        array(
            'cache',
            'logs',
        ),
        __DIR__,
);


FilePermissionCheck
~~~~~~~~~~~~~~~~~~~

Checks if given permissions are granted on the given files. This check only makes sense if used on file systems that
support permissions.

Configuration:

- check level
- an array of files or directories to check for
- an array of permissions to check - one or more of ['READ', 'WRITE', 'EXECUTE']
- base directory (optional) - if provided, all files/directories from the file array parameter will be expected relative
  to this directory.

Examples:

To raise an error if cache/ and log/ within the current directory are not readable or not writable use something like
this:

.. code-block:: php

    $check = new FilePermissionCheck(
        CheckOutcome::ERROR,
        array(
            'cache',
            'logs',
        ),
        array(
            'READ',
            'WRITE',
        ),
        __DIR__,
);


PhpModuleLoadedCheck
~~~~~~~~~~~~~~~~~~~~

Checks if certain PHP modules are loaded.

Configuration:

- check level
- an array of PHP modules; the names need to be provided in the same format which is output by `php -m`

Examples:

.. code-block:: php

    $check = new PhpModuleLoadedCheck(
        CheckOutcome::ERROR,
        array(
            'gd',
            'pdo_mysql',
            'xml',
        ),
    );


PhpRuntimeVersionCheck
~~~~~~~~~~~~~~~~~~~~~~

Checks if a valid PHP version is used.

Configuration:

* check level
* allowed PHP version or versions

The allowed PHP version can be configured in several ways:

* a single version string to allow all PHP versions from this version and up
* an array of version constraints. A version constraint is either a string as described directly above, or an array
  consisting of a version information and an operator to apply (">", ">=", "==", "!=", "<=" or "<").

Examples:

To allow PHP version 5.3.6 and above use something like this:

.. code-block:: php

    $check = new PhpRuntimeVersionCheck(
        CheckOutcome::ERROR,
        '5.3.6',
    );

To allow all PHP versions between 5.4.3 and 5.6.3 but not 5.5.3 use something like this:

.. code-block:: php

    $check = new PhpRuntimeVersionCheck(
        CheckOutcome::ERROR,
        array(
            '5.4.3',
            array(
                '5.6.3',
                '<',
            ),
            array(
                '5.5.3',
                '!=',
            ),
        ),
    );


Predefined Outputs
------------------

AbstractTranslatingCheckOutput
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Not an output class itself but an abstract base class that provides translation functionality. If you plan to write your
own output class, consider extending this class.

DefaultCheckOutput
~~~~~~~~~~~~~~~~~~

Uses `echo` statements to write to the current default output.

LogCheckOutput
~~~~~~~~~~~~~~

Writes to a configured logger.
When using this output, you will need to provide a configured instance of `Psr\\Log\\LoggerInterface`.

NullCheckOutput
~~~~~~~~~~~~~~~

Does not write anything. Use this if you think you need to :-)


Predefined Output Formatters
----------------------------

PlainOutputFormatter
~~~~~~~~~~~~~~~~~~~~

A simple pseudo-formatter that returns each line unchanged and has a "\n" line delimiter

HtmlOutputFormatter
~~~~~~~~~~~~~~~~~~~

A formatter that adds a span element along with a CSS class, depending on the outcome level. See the implementation
for details.

ConsoleOutputFormatter
~~~~~~~~~~~~~~~~~~~~~~

A formatter that uses the HTML-like Symfony console tags to decorate the output, depending on the outcome level. See the
implementation for details.


.. _installation chapter: https://getcomposer.org/doc/00-intro.md
