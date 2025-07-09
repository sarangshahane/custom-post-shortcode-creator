# Custom Post Shortcode Creator - Development Documentation

## Plugin Overview

**Custom Post Shortcode Creator** is a WordPress plugin that allows users to easily create and display custom post types using shortcodes. The plugin is specifically designed to work with the "workshop" custom post type and provides a comprehensive meta box system for managing workshop details including date/time and location information.

### Key Features
- **Shortcode Generation**: Creates `[workshops]` shortcode to display workshop posts
- **Meta Box System**: Advanced meta box for workshop details (date/time, location type, address)
- **Modern UI**: WordPress 6.8 compatible interface with responsive design
- **Internationalization**: Full translation support with POT files
- **Build System**: Grunt-based build process for asset optimization

## Folder Structure

```
custom-post-shortcode-creator/
├── .git/                          # Git repository
├── assets/                        # Frontend assets
│   ├── css/                       # Stylesheets
│   │   ├── meta-boxes.css         # Meta box styling
│   │   └── workshop-grid.css      # Workshop grid layout
│   ├── js/                        # JavaScript files
│   │   └── meta-boxes.js          # Meta box functionality
│   └── images/                    # Image assets
│       └── 400x400.jpg            # Default workshop image
├── classes/                       # PHP class files
│   ├── class-cpsc-loader.php      # Main plugin loader
│   ├── class-cpsc-shortcodes.php  # Shortcode functionality
│   ├── class-cpsc-meta-boxes.php  # Meta box system
│   └── index.php                  # Security file
├── languages/                     # Translation files (auto-generated)
├── .gitignore                     # Git ignore rules
├── changelog.txt                  # Version changelog
├── custom-post-shortcode-creator.php  # Main plugin file
├── Gruntfile.js                   # Grunt build configuration
├── LICENSE                        # Plugin license
├── package.json                   # NPM dependencies
├── readme.txt                     # WordPress.org readme
└── DEVELOPMENT.md                 # This documentation file
```

## Class Structure

### 1. Main Plugin File
**File**: `custom-post-shortcode-creator.php`
- **Purpose**: Plugin entry point and header information
- **Responsibilities**: 
  - Define plugin constants
  - Load the main loader class
  - Plugin activation/deactivation hooks

### 2. Loader Class
**File**: `classes/class-cpsc-loader.php`
- **Class**: `Cpsc_Loader`
- **Pattern**: Singleton
- **Purpose**: Central plugin initialization and file loading
- **Responsibilities**:
  - Define plugin constants
  - Load required class files
  - Handle plugin activation/deactivation
  - Load text domain for translations
  - Initialize plugin components

### 3. Shortcodes Class
**File**: `classes/class-cpsc-shortcodes.php`
- **Class**: `Cpsc_Frontend`
- **Pattern**: Singleton
- **Purpose**: Handle frontend shortcode functionality
- **Responsibilities**:
  - Register `[workshops]` shortcode
  - Query and display workshop posts
  - Generate HTML output for workshop grid
  - Enqueue frontend styles and scripts

### 4. Meta Boxes Class
**File**: `classes/class-cpsc-meta-boxes.php`
- **Class**: `Cpsc_Meta_Boxes`
- **Pattern**: Singleton
- **Purpose**: Manage workshop post meta boxes
- **Responsibilities**:
  - Add meta box to workshop post type
  - Handle meta box data saving
  - Provide getter methods for meta data
  - Enqueue admin scripts and styles
  - AJAX handling for dynamic fields

## Code Categorization by Function

### 🔧 **Core System**
- **Loader Class**: Plugin initialization, constants, file loading
- **Main Plugin File**: Entry point, hooks, basic setup

### 🎨 **Frontend Display**
- **Shortcodes Class**: Shortcode registration and rendering
- **CSS Files**: Styling for workshop grid and meta boxes
- **JavaScript Files**: Interactive functionality

### ⚙️ **Admin Interface**
- **Meta Boxes Class**: Workshop details management
- **Admin CSS**: Meta box styling and WordPress 6.8 compatibility
- **Admin JavaScript**: Dynamic field behavior

### 🌐 **Internationalization**
- **Text Domain**: `custom-post-shortcode-creator`
- **Translation Files**: POT, PO, MO files in languages directory
- **Grunt Tasks**: i18n automation

### 🛠️ **Build System**
- **Gruntfile.js**: Asset minification, RTL support, version management
- **Package.json**: Dependencies and build scripts
- **Build Tasks**: CSS/JS minification, RTL generation, release packaging

### 🔒 **Security & Standards**
- **Nonce Verification**: All form submissions
- **Data Sanitization**: Input validation and cleaning
- **WordPress Standards**: PHPCS, PHPStan compliance
- **Access Control**: User capability checks

## Meta Box Fields

### Workshop Date
- **Field Type**: HTML5 `date`
- **Meta Key**: `_cpsc_workshop_start_date`
- **Required**: Yes
- **Purpose**: Set workshop date for upcoming events

### Workshop Start Time
- **Field Type**: HTML5 `time`
- **Meta Key**: `_cpsc_workshop_start_time`
- **Required**: Yes
- **Purpose**: Set workshop start time

### Workshop End Time
- **Field Type**: HTML5 `time`
- **Meta Key**: `_cpsc_workshop_end_time`
- **Required**: Yes
- **Purpose**: Set workshop end time

### Location Type
- **Field Type**: Dropdown select
- **Meta Key**: `_cpsc_workshop_location_type`
- **Options**: 
  - `online` - Online (Via Zoom)
  - `offline` - Offline
- **Default**: `online`

### Location Address
- **Field Type**: Textarea
- **Meta Key**: `_cpsc_workshop_location_address`
- **Conditional**: Only shown when location type is "offline"
- **Purpose**: Physical address for offline workshops

## Build System Tasks

### Available Grunt Tasks
- `grunt minify` - Minify CSS and JavaScript files
- `grunt style` - Apply PostCSS processing (autoprefixer, flexibility)
- `grunt rtl` - Generate RTL CSS files
- `grunt release` - Create plugin release package
- `grunt textdomain` - Add text domain to PHP files
- `grunt i18n` - Generate translation files
- `grunt version-bump --ver=<version>` - Update version numbers

### NPM Scripts
- `npm run build` - Build all assets
- `npm run release` - Create release package
- `npm run i18n:all` - Generate all translation files
- `npm run pretty:fix` - Format code with Prettier
- `npm run lint-js:fix` - Fix JavaScript linting issues

## Development Workflow

### 1. Setup
```bash
cd wp-content/plugins/custom-post-shortcode-creator
npm install
```

### 2. Development
- Edit source files in `assets/css/` and `assets/js/`
- Run `npm run build` to minify assets
- Test meta box functionality in WordPress admin

### 3. Release Process
```bash
npm run build
grunt version-bump --ver=1.0.1
npm run release
```

### 4. Translation
```bash
npm run i18n:all
# Edit .po files in languages directory
npm run i18n:mo
```

## Coding Standards

### PHP Standards
- WordPress Coding Standards (WPCS)
- PHPStan level 8 compliance
- Proper sanitization and validation
- Comprehensive inline documentation

### JavaScript Standards
- WordPress ESLint configuration
- Prettier code formatting
- Modern ES6+ syntax
- jQuery for DOM manipulation

### CSS Standards
- WordPress Stylelint configuration
- BEM methodology for class naming
- Responsive design principles
- WordPress 6.8 color scheme compliance

## Plugin Constants

```php
define( 'CPSC_FILE', __FILE__ );
define( 'CPSC_BASE', plugin_basename( CPSC_FILE ) );
define( 'CPSC_DIR', plugin_dir_path( CPSC_FILE ) );
define( 'CPSC_URL', plugins_url( '/', CPSC_FILE ) );
define( 'CPSC_VER', '1.0.0' );
define( 'CPSC_SLUG', 'custom-post-shortcode-creator' );
define( 'CPSC_NAME', 'Custom Post Shortcode Creator' );
```

## Hooks and Filters

### Actions
- `cpsc_loaded` - Fired when plugin is fully loaded
- `cpsc_init` - Fired when plugin is initialized
- `add_meta_boxes` - Register meta boxes
- `save_post` - Save meta box data
- `admin_enqueue_scripts` - Enqueue admin assets
- `wp_enqueue_scripts` - Enqueue frontend assets

### Filters
- `cpsc_languages_directory` - Customize languages directory path
- `plugin_locale` - WordPress locale filter

## Database Schema

### Meta Fields
- `_cpsc_workshop_start_date` (VARCHAR) - Workshop start date
- `_cpsc_workshop_start_time` (VARCHAR) - Workshop start time
- `_cpsc_workshop_end_time` (VARCHAR) - Workshop end time
- `_cpsc_workshop_location_type` (VARCHAR) - Location type (online/offline)
- `_cpsc_workshop_location_address` (TEXT) - Physical address for offline workshops

## Browser Support

- **Modern Browsers**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **HTML5 Features**: datetime-local input, CSS Grid, Flexbox
- **JavaScript**: ES6+ with jQuery fallback
- **CSS**: CSS3 with autoprefixer support

## Performance Considerations

- **Asset Minification**: CSS and JS files are minified for production
- **Conditional Loading**: Admin scripts only load on relevant pages
- **Efficient Queries**: Optimized WP_Query for workshop display
- **Caching Ready**: Compatible with WordPress caching plugins

## Security Features

- **Nonce Verification**: All form submissions protected
- **Capability Checks**: User permission validation
- **Data Sanitization**: Input cleaning and validation
- **SQL Injection Prevention**: Prepared statements and WordPress functions
- **XSS Protection**: Output escaping with WordPress functions

---

*This documentation is maintained for AI agents and developers working on the Custom Post Shortcode Creator plugin. For questions or contributions, please refer to the plugin's main repository.* 