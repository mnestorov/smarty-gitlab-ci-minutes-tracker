# SM - GitLab CI Minutes Tracker

[![Licence](https://img.shields.io/badge/LICENSE-GPL2.0+-blue)](./LICENSE)

- **Developed by:** Martin Nestorov 
    - Explore more at [nestorov.dev](https://github.com/mnestorov)
- **Plugin URI:** https://github.com/mnestorov/smarty-gitlab-ci-minutes-tracker

## Overview

A modern WordPress plugin that tracks GitLab compute usage quotas with real-time pipeline analytics and beautiful dashboards.

![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple)
![License](https://img.shields.io/badge/License-GPL--2.0-green)
![Version](https://img.shields.io/badge/Version-3.1.0-orange)

## ✨ Features

### 🎯 **Core Functionality**
- **Real-time GitLab Usage Tracking** - Monitor compute usage quotas that match GitLab's Usage Quotas dashboard
- **Multiple Source Support** - Track multiple GitLab groups and namespaces simultaneously
- **Pipeline-based Calculation** - Accurate usage calculation from actual pipeline job durations
- **WordPress Dashboard Integration** - Beautiful dashboard widget with usage overview

### 🎨 **Modern Interface**
- **WordPress Blue Theme** - Native WordPress design language with professional styling
- **Responsive Design** - Works perfectly on desktop, tablet, and mobile devices
- **Interactive Dashboard** - Live data table with progress bars and status indicators
- **Setup Guidance** - Clear onboarding for unconfigured states

### 📊 **Analytics & Monitoring**
- **Usage Progress Tracking** - Visual progress bars and percentage indicators
- **Status Monitoring** - Color-coded status badges (Normal, High, Error)
- **Error Handling** - Comprehensive error reporting with helpful messages
- **Data Caching** - Efficient API usage with intelligent caching

### 🔧 **Developer Features**
- **Clean Architecture** - Separated CSS, JS, and PHP for maintainability
- **WordPress Standards** - Follows WordPress coding standards and best practices
- **Translation Ready** - Full internationalization support with proper text domains
- **Extensible Design** - Well-structured code for easy customization

## 🚀 Installation

### Manual Installation

1. **Download** the plugin files
2. **Upload** the entire `smarty-gitlab-ci-minutes-tracker` folder to `/wp-content/plugins/`
3. **Activate** the plugin through the 'Plugins' menu in WordPress
4. **Configure** your GitLab settings (see Configuration section)

### Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **GitLab**: Any version with API access
- **Permissions**: `manage_options` capability for configuration

## ⚙️ Configuration

### Step 1: GitLab Personal Access Token

1. Go to **GitLab** → **User Settings** → **Access Tokens**
2. Create a new token with **`read_api`** scope
3. Copy the generated token (starts with `glpat-`)

### Step 2: Plugin Setup

1. Navigate to **WordPress Admin** → **GitLab CI**
2. Paste your **Personal Access Token** in the API Configuration section
3. **Add Sources** - Configure the GitLab groups or namespaces you want to track:
   - **Display Name**: Friendly name for identification
   - **Type**: Choose "Group" or "Namespace"
   - **GitLab ID/Path**: The group name or numeric ID from GitLab

### Step 3: Verification

1. **Save Settings** and check for any error messages
2. **View Dashboard** → Navigate to WordPress Dashboard to see the GitLab CI Usage widget
3. **Check Data** → Verify that usage data matches your GitLab Usage Quotas dashboard

## 📊 Usage

### Dashboard Widget

The plugin adds a **GitLab CI Usage** widget to your WordPress dashboard that displays:

- **Source Information** - Name and type of each tracked GitLab source
- **Current Usage** - Compute units used in the current billing period
- **Usage Limits** - Total compute units available
- **Progress Bars** - Visual representation of usage percentage
- **Status Indicators** - Color-coded status badges

### Data Interpretation

- **Normal** (Green) - Usage below 90% of limit
- **High** (Yellow) - Usage above 90% of limit  
- **Error** (Red) - API connection or permission issues

### Troubleshooting

#### Common Issues

**"No data available"**
- Check your GitLab token has `read_api` permissions
- Verify the group/namespace ID is correct
- Ensure the group/namespace exists and you have access

**"API Error"**
- Verify your GitLab token is valid and hasn't expired
- Check network connectivity to GitLab.com
- Confirm the group/namespace hasn't been renamed or deleted

**"Settings not saving"**
- Ensure you have `manage_options` capability
- Check for PHP errors in your error logs
- Verify WordPress nonce validation isn't failing

## 🔌 API Integration

### GitLab API Endpoints Used

- **Groups API**: `/api/v4/groups/{id}?statistics=true`
- **Projects API**: `/api/v4/groups/{id}/projects`
- **Pipelines API**: `/api/v4/projects/{id}/pipelines`
- **Jobs API**: `/api/v4/projects/{id}/pipelines/{id}/jobs`

### Data Calculation

The plugin calculates usage by:

1. **Fetching** all projects within the specified group
2. **Retrieving** pipelines from the current billing period
3. **Analyzing** individual job durations
4. **Filtering** for shared runner jobs only
5. **Converting** seconds to compute minutes
6. **Aggregating** total usage across all projects

### Caching Strategy

- **Transient Cache**: 5-minute cache for API responses
- **Conditional Loading**: Assets only load on relevant admin pages
- **Efficient Queries**: Minimizes API calls with intelligent caching

## 🛠️ Development

### File Structure

```
smarty-gitlab-ci-minutes-tracker/
├── css/
│   └── smarty-gl-admin.css          # Modern WordPress styling
├── js/
│   └── smarty-gl-admin.js           # Interactive functionality
├── CHANGELOG.md                     # Version history
├── LICENSE                          # GPL-2.0 license
├── README.md                        # This file
├── index.php                        # Security protection
└── smarty-gitlab-ci-minutes-tracker.php # Main plugin file
```

### Key Functions

- `smarty_gl_fetch_gitlab_data()` - Main API integration function
- `smarty_gl_calculate_pipeline_usage()` - Usage calculation logic
- `smarty_gl_dashboard_widget_content()` - Dashboard widget rendering
- `smarty_gl_settings_page()` - Admin settings interface

### Hooks & Filters

The plugin uses standard WordPress hooks:

- `admin_menu` - Adds admin menu pages
- `admin_init` - Registers settings and fields
- `admin_enqueue_scripts` - Loads CSS/JS assets
- `wp_dashboard_setup` - Adds dashboard widget

### Translation

The plugin is fully translatable with proper text domains:

- **Text Domain**: `smarty-gitlab-ci-minutes-tracker`
- **Domain Path**: `/languages`
- **Functions Used**: `__()`, `esc_html__()`, `esc_attr__()`

## 📝 Changelog

For a detailed list of changes and updates made to this project, please refer to our [Changelog](./CHANGELOG.md).

## 🤝 Contributing

We welcome contributions! Please:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** your changes (`git commit -m 'Add amazing feature'`)
4. **Push** to the branch (`git push origin feature/amazing-feature`)
5. **Open** a Pull Request

### Development Guidelines

- Follow **WordPress Coding Standards**
- Use **proper internationalization** for all user-facing strings
- Write **comprehensive comments** for complex functions
- Test with **multiple GitLab configurations**
- Ensure **responsive design** compatibility

## 🆘 Support

### Getting Help

- **Documentation**: Check this README and inline code comments
- **Issues**: Report bugs or request features via GitHub Issues
- **WordPress**: Ensure you're using a supported WordPress version

### System Requirements

- **WordPress**: 5.0+ (tested up to 6.4)
- **PHP**: 7.4+ (recommended: 8.0+)
- **MySQL**: 5.6+ or MariaDB 10.1+
- **GitLab**: Any version with API v4 support

## 🔗 Links

- **GitLab API Documentation**: [https://docs.gitlab.com/ee/api/](https://docs.gitlab.com/ee/api/)
- **WordPress Plugin Development**: [https://developer.wordpress.org/plugins/](https://developer.wordpress.org/plugins/)
- **WordPress Coding Standards**: [https://developer.wordpress.org/coding-standards/](https://developer.wordpress.org/coding-standards/)

---

**Made with ❤️ for the WordPress community**

*Track your GitLab compute usage with style and precision!*

---

## 📄 License

This project is released under the [GPL-2.0+ License](http://www.gnu.org/licenses/gpl-2.0.txt).
