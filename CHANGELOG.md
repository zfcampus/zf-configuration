# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.3.0 - 2017-08-24

### Added

- [#22](https://github.com/zfcampus/zf-configuration/pull/22) adds support for
  zend-config v3 releases.

- [#23](https://github.com/zfcampus/zf-configuration/pull/23) adds support for
  PHP 7.1 and the upcoming 7.2 release.

### Deprecated

- Nothing.

### Removed

- [#23](https://github.com/zfcampus/zf-configuration/pull/23) removes support
  for HHVM.

### Fixed

- Nothing.

## 1.2.2 - TBD

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.2.1 - 2016-08-13

### Added

- [#19](https://github.com/zfcampus/zf-configuration/pull/19) adds the ability
  to enable usage of `::class` notation in generated configuration via a
  configuration setting, `zf-configuration.class_name_scalars`.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#19](https://github.com/zfcampus/zf-configuration/pull/19) fixes a syntax
  error in the `ConfigResourceFactory`.

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
