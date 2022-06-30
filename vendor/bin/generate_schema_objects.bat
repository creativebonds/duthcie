@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../gmostafa/php-graphql-oqm/bin/generate_schema_objects
php "%BIN_TARGET%" %*
