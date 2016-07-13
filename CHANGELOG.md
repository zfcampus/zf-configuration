# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.2.0 - 2016-07-13

### Added

- [#17](https://github.com/zfcampus/zf-configuration/pull/17) adds support for v3
  releases of Zend Framework components, while retaining compatibility with v2
  releases.
- [#17](https://github.com/zfcampus/zf-configuration/pull/17) extracts all
  factories previously defined inline in the `Module` class into their own classes:
  - `ZF\Configuration\ConfigResourceFactory`
  - `ZF\Configuration\ConfigWriterFactory`
  - `ZF\Configuration\ModuleUtilsFactory`
  - `ZF\Configuration\ResourceFactoryFactory`

### Deprecated

- Nothing.

### Removed

- [#17](https://github.com/zfcampus/zf-configuration/pull/17) removes support
  for PHP 5.5.

### Fixed

- Nothing.
