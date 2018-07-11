Specification
=============

CheckHandler and CheckHandlerInterface
--------------------------------------

The main class of the sanity check bundle is the CheckHandler class. It performs a given set of checks and returns corresponding check outcomes.
If an exception occurs during a check, a CheckOutcome object with level CheckOutcome::Exception is added to the result list, and the check goes on.
The CheckHandler class is registered as a Symfony service named chameleon_system_sanitycheck.check_handler.
Its interface is defined as CheckHandlerInterface.

Methods
.......

public function checkAll()

Performs all registered checks.

:Parameters:

    none

:Return value:

    array(CheckOutcome)|array()


public function checkSome(array $checkList) return array(CheckOutcome)|array()

Performs a check on the given check items.

:Parameters:

    `checkList`: A list of checks to perform. The parameter may contain check identifiers and/or bundle names in camel-case bundle name (e.g. "AcmeDemoBundle") or the bundle alias (e.g. "acme_demo").

:Return value:

    array(CheckOutcome)|array()

public function checkSingle(string $checkName) return array(CheckOutcome)|array()

Performs a check on the given check name

:Parameters:

    `checkName`: $checkName can be a check identifier and/or a bundle name in camel-case bundle name (e.g. "AcmeDemoBundle") or the bundle alias (e.g. "acme_demo").

:Return value:

    array(CheckOutcome)|array()


CheckInterface
--------------

The CheckInterface represents a single check. Concrete implementations define what to check, given individual configuration data (e.g. there is a check that finds out if a directory is writable or a configuration parameter holds an expected value).

Methods:

public function setConfiguration(array $configuration)

Sets the configuration for this check. The $configuration array contains all the values needed to perform the check.
The bundle provides some check implementations. Custom check types can be added arbitrarily.

Methods:

public function performCheck() return array(CheckOutcome)

Performs this check and returns one or more CheckOutcome objects.


CheckOutcome
------------

The Outcome class defines the result of a single check.
The CheckOutcome class also defines a number of constants that represent the importance/severity of a check outcome.

Constants:

The constants are semantically ordered from low to high, so you can react to outcomes that are "at least as severe as", e.g. "display all warnings and errors" can be expressed by ">= CheckOutcome::WARNING"
CheckOutcome::OK
CheckOutcome::NOTICE
CheckOutcome::WARNING
CheckOutcome::ERROR

Properties:

private $messageKey The lookup key of the message to display/log
private $messageParameters Optional parameters to insert into the translated message
private $level The machine-readable outcome of the check. This is one of the constants described below. The interpretation of the outcome is left to the caller.

Methods:

public function __construct($messageKey, array $messageParameters, $level)
All getters
No setters

This class is a simple (immutable) value holder and may not be used to fulfil an active role.


CheckResolver and CheckResolverInterface
----------------------------------------

The CheckResolver resolves Check instances for given names. In order to be found by this class, a check needs to be tagged with chameleon_system.sanitycheck

Methods:

public function findChecksForName(string $name) return array(CheckInterface)|array()
Finds checks that are identified by the given name. $name may be a Symfony service identifier or a bundle name (e.g. "@AcmeDemoBundle").

public function findChecksForNameList(array(string) $name) return array(CheckInterface)|array()
Finds checks for every name in the $name array. Each name may be a Symfony service identifier or a bundle name.

public function findAllChecks() return array(CheckInterface)|array()
Finds all checks defined in the application.


CheckSuite
----------

A CheckSuite is a handler that performs checks and returns the outcome in a custom format. Its main purpose is to ease configuration of checks.

Constructor:
public function __construct($level, $checkOutput, array(string) $checks)
$checks is an array of strings that contains check identifiers
$level defines a minimum outcome level to be recognized. See CheckOutcomeFilter.
$checkOutput defines what to do with the CheckOutcomes.

public function performChecks()
Performs the $checks and uses the $checkOutput to write all outcomes of level $level and higher.


CheckOutcomeFilter
------------------

The CheckOutcomeFilter allows to reduce the number of CheckOutcomes, e.g. to display only the most important ones.
It provides the following methods:

public static function filterOutcomes(array(CheckOutcome) $outcomes, $level)
with $level being one of the constants defined in the CheckOutcome class (see above).
This method returns an array of CheckOutcome objects. Each CheckOutcome's level in this array is equal to or greater than the given $level.


CheckOutputInterface
--------------------

This interface defines an output service.

public function output(array(CheckOutcome) $outcomes)

public function outputSingle(CheckOutcome $outcome)

There will be a number of output classes:
- RawCheckOutput (simply returns the given array)
- LogCheckOutput (writes the outcomes to a log)
- SimpleHtmlCheckOutput (writes a simple HTML representation)

Other output classes can be written, or a client may handle output manually.


Configuration
=============

Configure Checks
----------------

A compiler pass named AddSanityChecksPass is used to collect all the checks defined in the project.
It collects all services tagged with "chameleon_system.sanity_check.check".


Check Call Sequence
===================

* caller calls CheckHandlerInterface::checkSome() and provides a set of checks to perform.
* CheckHandler resolves the concrete Check instances from the given names.
* CheckHandler performs the single checks and collects the CheckOutcome objects they return.
* CheckHandler returns the CheckOutcome objects.


Appendix: Check Implementations
===============================

PingExternalCheck
-----------------

Checks if an external system is available

FileExistsCheck
---------------

Checks if one or more files exist.
Optional it can be checked if they are of a given type (file, directory, symlink, ...)

FilePermissionCheck
-------------------

Checks if one or more files have the given permissions (e.g. are writable for the run user).
Does also check if the file exists.

ConfigurationFileCheck
----------------------

Checks if a configuration file contains a given parameter with a given value.

PhpConfigurationCheck
---------------------

Checks if a given PHP parameter is set on a given value.

DiskSpaceCheck
--------------

Checks if the given disk has at least a certain amount of space left.
