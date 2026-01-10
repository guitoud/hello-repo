# jQuery Mon Projet

A modernized jQuery plugin project with updated tooling and practices.

## Modernization Updates

This project has been updated to use:
- Modern Grunt (v1.5.3)
- ESLint instead of JSHint
- Karma + QUnit for testing
- Updated dependencies
- AMD/CommonJS compatibility
- Modern QUnit syntax

## Getting Started

Install dependencies:
```bash
npm install
```

Build the project:
```bash
grunt build
```

Run tests:
```bash
grunt test
```

Develop interactively:
```bash
grunt dev
```

In your web page:

```html
<script src="jquery.js"></script>
<script src="dist/jquery.mon-projet.min.js"></script>
<script>
jQuery(function($) {
  $.mon_projet(); // "awesome."
});
</script>
```

## API Documentation

### $.mon_projet(options)

#### options

Type: `Object`
Default: `{ punctuation: '.' }`

A collection of settings that will be merged with the default settings.

##### options.punctuation

Type: `String`
Default: `'.'`

What to punctuate the returned string with.

Returns: `String` - `"awesome."` by default.

### $.fn.mon_projet()

Returns: `jQuery.selection`

Perform some action on the selected elements.

```javascript
$(document.body).mon_projet()
```

## Release History
* 2026-01-11 - v1.0.0 - Modernized project with updated tooling
* 2015-01-11 - v0.1.0 - Initial release
