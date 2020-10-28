# nz.co.fuzion.multipleeventreg

Registers participant to multiple events based on the event ids entered by the user on the custom field.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.0+
* CiviCRM (*FIXME: Version number*)

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl nz.co.fuzion.multipleeventreg@https://github.com/FIXME/nz.co.fuzion.multipleeventreg/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/nz.co.fuzion.multipleeventreg.git
cv en multipleeventreg
```

## Usage

- On installation, the extension creates a new custom field on the "Event" entity.
- Enter comma-separated event ids on the custom text field. So eg if you're creating an event E1 and want participants to also register to E2 and E3, enter ids for E2, E3 on the text field.
- Register a partcipant on E1. The participant should also register to E2 and E3.

## Known Issues

(* FIXME *)
