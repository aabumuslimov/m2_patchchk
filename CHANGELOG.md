1.6.4
-----
* Fixed bugs:
    * Fixed the issue with Undefined variable: result in /.../design/templates/index.phtml on line 25

1.6.3
-----
* Fixed bugs:
    * Updated a dependency for the patch converter tool (issue with convert renamed file names - https://github.com/isitnikov/m2-convert-patch-for-composer-install/pull/13)

1.6.2
-----
* Fixed bugs:
    * Updated a dependency for the patch converter tool (https://github.com/isitnikov/m2-convert-patch-for-composer-install/issues/11)

1.6.1
-----
* Fixed bugs:
    * Fixed an issue with patch path for git apply strategy validation

1.6.0
-----
* Fixed bugs:
    * Implemented change for cloud patch validation. Now it will preserve an original patch format without extra conversions.
    * Fixed created var/* folders permissions issue. Now 02777 instead of 0777 is set
* Improvements:
    * Implemented a separate validation for "git apply" used at cloud patch apply strategy
    * Implemented static content basic versioning to prevent loading outdated browser cache content after upgrade
    * Implemented dependency management via composer
    * Updated a dependency for the patch converter tool (https://github.com/isitnikov/m2-convert-patch-for-composer-install/issues/9)
    * Added info block about the tool version
    * Added *.diff extension to a supported file formats
    * Code refactoring, unused code cleaning
    * UI minor improvements

1.5
-----
* Improvements:
    * Added validation for already merged changes by running patch revert in dry-run mode
    * Added .htaccess to var/ and vendor/
    * Implemented legend description for statuses and releases grid.
