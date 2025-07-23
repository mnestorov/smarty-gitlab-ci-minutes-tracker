# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/en/2.0.0/).

## [3.1.2] - 2025-07-23

### Fixed
- **Critical Architecture Issue**: Completely eliminated all inline JavaScript from PHP files
- **Performance Problem**: Dashboard now loads instantly without blocking API calls
- **AJAX Reliability**: Fixed dashboard widget not updating due to missing ajaxurl variable
- **Code Standards Violation**: Removed all embedded script tags from PHP output

### Changed
- **Complete JS Refactor**: Moved from inline scripts to clean data-attribute approach
- **External JS Architecture**: All dashboard functionality now in `smarty-gl-admin.js`
- **Proper AJAX Handling**: Added fallback AJAX URL detection and comprehensive error handling
- **Clean Separation**: PHP generates clean HTML with data attributes, JS reads and acts

### Improved
- **WordPress Best Practices**: Now follows WordPress coding standards for JS/PHP separation
- **Debugging**: Added comprehensive console logging for troubleshooting
- **Error Handling**: Graceful fallbacks for failed AJAX requests and missing DOM elements
- **Maintainability**: Clean, readable code structure with proper function separation
- **Performance**: Eliminated render-blocking inline JavaScript

### Removed
- All inline `<script>` tags from PHP files
- Test AJAX handlers and debugging code
- Embedded JavaScript within HTML output

## [3.1.1] - 2025-07-23

### Fixed
- **Performance Issue**: Fixed slow dashboard loading by implementing asynchronous data loading
- **Calculation Accuracy**: Corrected GitLab usage calculation limits (was showing 35/400 instead of correct 97/400)
- **Code Organization**: Moved inline JavaScript from PHP to separate `smarty-gl-admin.js` file

### Changed
- Dashboard now loads instantly with cached data, updates in background
- Increased API limits: projects (20→100), pipelines (10→50), per_page (20→100) for accurate calculations
- Improved error handling with proper timeout messages and retry functionality
- Better separation of concerns - JavaScript functions moved to external files

### Improved
- Better user experience with loading indicators and graceful error handling
- Reduced inline code in PHP for cleaner architecture
- Enhanced caching strategy with separate calculation cache (30 minutes)
- Proper translation support for JavaScript strings via wp_localize_script

## [3.1.0] - 2025-07-23

### Added
- Modern WordPress Blue theme design replacing GitLab orange
- Separated CSS and JS files for better maintainability (`css/smarty-gl-admin.css`, `js/smarty-gl-admin.js`)
- Enhanced dashboard widget with professional data table view
- Improved source field styling with grid layout and better labels
- Better error handling and user feedback throughout the interface
- Dashicons integration for menu icon (`dashicons-chart-line`)
- Comprehensive README.md documentation
- Full internationalization support with proper text domains

### Fixed
- Settings page registration errors ("options page not in allowed list")
- Asset loading optimization (conditional loading on relevant pages only)
- Form validation improvements with visual error indicators
- Dashboard widget styling for unconfigured states

### Changed
- Complete asset architecture overhaul - removed embedded CSS/JS from PHP
- Updated all function prefixes from `smarty_` to `smarty_gl_`
- Text domain changed to `smarty-gitlab-ci-minutes-tracker`
- Improved responsive design for mobile and tablet devices
- Enhanced setup guidance with clear onboarding cards

### Improved
- Performance optimizations with better caching strategy
- Clean separation of concerns (PHP logic, CSS styling, JS functionality)
- WordPress coding standards compliance
- Professional UI/UX following WordPress design patterns

## [3.0.0] - 2025-07-23

### Added
- Complete plugin rewrite with modern architecture
- Pipeline-based usage calculation for accurate GitLab usage tracking
- Support for GitLab groups and namespaces (not just individual projects)
- Real-time dashboard integration with WordPress admin
- Multiple source tracking capability
- Advanced error handling and status reporting

### Changed
- Moved from project-level to group/namespace-level tracking
- Updated to use GitLab Groups API instead of Projects API
- Implemented automatic pipeline usage calculation
- Modern settings interface with dynamic source management

### Fixed
- API compatibility with current GitLab versions
- Compute units calculation to match GitLab's Usage Quotas dashboard
- Headers already sent errors
- Data accuracy issues

## [2.0.0] - 2025-07-23

### Added
- Groups API integration for organization-level tracking
- Multiple source tracking with dynamic add/remove functionality
- Enhanced caching mechanism for better performance

### Improved
- Better error handling and user feedback
- API response processing and data validation
- Settings page interface and user experience

### Fixed
- Manual input removal (automated calculation only)
- API endpoint compatibility issues

## [1.0.0] - 2025-07-23

### Added
- Initial release of the Smarty GitLab CI Minutes Tracker plugin
- Basic functionality to fetch and display GitLab CI minutes for specified projects
- Admin settings page to configure GitLab API URL, Private Token, and Project IDs
- Simple dashboard widget to show an overview of CI minutes
- Basic caching mechanism for GitLab API responses to improve performance
- Foundation for future enhancements

### Technical Details
- WordPress 5.0+ compatibility
- PHP 7.4+ support
- GPL-2.0-or-later license
- Basic GitLab API v4 integration