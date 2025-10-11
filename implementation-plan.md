# Laravel Multi-Tenant Admin Panel Implementation Plan

## Overview
This document outlines the step-by-step plan to recreate the functionality from the old Laravel 5.8 application in the current Laravel 12 application using Filament as the admin panel framework, with **MULTI-TENANCY** as a core architectural requirement.

## Multi-Tenancy Architecture
The new system will be a **multi-tenant application** where each tenant (company/organization) has completely isolated data. This means:
- All data must be strictly filtered by tenant
- No cross-tenant data access
- Tenant-specific configurations and settings
- Isolated user management per tenant
- Separate file storage per tenant

## Analysis Summary

### Old Version Key Features:
1. **Order Management System** - Core business logic for managing orders, clients, projects
2. **Article/Component Management** - Inventory and product management
3. **User Management & Permissions** - Role-based access control
4. **Document Management** - File uploads and document categorization
5. **Schedule Management** - Montage scheduling and team management
6. **Financial Tracking** - Revenue, expenses, and balance calculations
7. **Reporting System** - Various reports and analytics

### Current Version Status:
- Laravel 12 with Filament 4.0 admin panel
- Basic user management with Spatie permissions
- Project, Component, Assembly, Article, Piece models already exist
- Filament resources partially implemented

## Multi-Tenancy Implementation Strategy

### Tenant Isolation Methods
We'll use **Database-per-tenant** approach with **Stancl/Tenancy** for optimal isolation:

1. **Tenant Model**: Each tenant gets a unique database
2. **Database Switching**: Automatic database switching per tenant
3. **Middleware**: Tenant identification and database switching
4. **File Storage**: Tenant-specific storage paths
5. **Complete Isolation**: Each tenant has separate database

### Dependencies for Multi-Tenancy
```json
{
  "stancl/tenancy": "^3.0"
}
```

## Multi-Tenancy Implementation Details

### Tenant Identification Methods
1. **Subdomain-based**: `tenant1.yourapp.com`, `tenant2.yourapp.com` (for web browsers)
2. **Header-based**: `X-Tenant-ID` header for API access and programmatic access

### Tenant Isolation Strategy
- **Database Level**: Each tenant has separate database (complete isolation)
- **Database Switching**: Automatic database switching per tenant
- **Middleware**: Tenant identification and database switching
- **File Storage**: Tenant-specific storage paths (`storage/tenant-{id}/`)
- **Caching**: Tenant-specific cache keys
- **Sessions**: Tenant-aware session management

### Cross-Tenant Security Measures
- **Database Isolation**: Complete database separation prevents cross-tenant access
- **File Access**: Tenant-specific file storage with access controls
- **API Endpoints**: Tenant-aware API responses with header-based identification
- **User Management**: Users can only access their assigned tenant databases
- **Data Validation**: Cross-tenant data access impossible due to database separation

## Implementation Plan

### Phase 0: Multi-Tenancy Foundation (Week 1)

#### 0.1 Multi-Tenancy Setup ✅ **COMPLETED**
- [x] **Install Stancl/Tenancy Package**
  - Install Stancl/Tenancy v3.0+
  - Configure subdomain-based tenant identification
  - Configure header-based tenant identification for API access
  - Set up database switching middleware

- [x] **Tenant Model & Migration**
  - Fields: id, name, domain, subdomain, database, is_active, created_at, updated_at
  - Tenant identification and database switching logic
  - Tenant-specific configuration storage

- [x] **Database Switching Setup**
  - Configure automatic database switching per tenant
  - Set up tenant database creation and management
  - Implement tenant context middleware

#### 0.2 Tenant-Aware Models ✅ **COMPLETED**
- [x] **Update All Models**
  - Remove `tenant_id` fields (not needed with database-per-tenant)
  - Models automatically tenant-aware through database switching
  - Update all relationships to work with database switching
  - Create tenant-aware factories and seeders

- [x] **Database Switching Management**
  - Middleware for tenant identification and database switching
  - Tenant database creation and management
  - Tenant-specific file storage
  - Tenant-aware caching

**📋 Detailed Implementation**: See `multi-tenancy-implementation-plan.md` for complete technical details.

### Phase 1: Database Schema & Models (Week 2-3)

#### 1.1 Create Missing Models and Migrations (All Tenant-Aware) ✅ **COMPLETED**
- [x] **Order Model & Migration**
  - Fields: number, description, install_date, install_date_confirmed, price_to_customer, price_to_supplier, budget, montage_time, is_requested, is_confirmed, is_delivered, is_finished, created_at, updated_at
  - Relationships: belongsTo(User, Client, Place, OrderCategory, Project), hasMany(Document, Montage), belongsToMany(Article)
  - Automatically tenant-aware through database switching

- [x] **Client Model & Migration**
  - Fields: name, phone, email, company_id
  - Relationships: belongsTo(Company), hasMany(Order)
  - Automatically tenant-aware through database switching

- [x] **Company Model & Migration**
  - Fields: name, address, vat, is_supplier, is_client
  - Relationships: hasMany(Client)
  - Automatically tenant-aware through database switching

- [x] **Place Model & Migration**
  - Fields: name, address, coordinates
  - Relationships: hasMany(Order)
  - Automatically tenant-aware through database switching

- [x] **OrderCategory Model & Migration**
  - Fields: program_name, humanlike_name, properties (JSON)
  - Relationships: hasMany(Order)
  - Automatically tenant-aware through database switching

- [x] **Document Model & Migration**
  - Fields: file_name, description, price, date, is_paid, type, contragent_type, contragent_id, order_id, document_category_id
  - Relationships: belongsTo(Order, DocumentCategory), morphTo(contragent)
  - Automatically tenant-aware through database switching
  - Tenant-specific file storage paths

- [x] **DocumentCategory Model & Migration**
  - Fields: program_name, humanlike_name, type, attributes (JSON)
  - Relationships: hasMany(Document)
  - Automatically tenant-aware through database switching

- [x] **Montage Model & Migration**
  - Fields: date, duration, confirmed, user_id, order_id, man_day_id
  - Relationships: belongsTo(User, Order, ManDay)
  - Automatically tenant-aware through database switching

- [x] **ManDay Model & Migration**
  - Fields: date, is_vacation, is_medical, price, description, user_id
  - Relationships: belongsTo(User), hasMany(Montage)
  - Automatically tenant-aware through database switching

- [x] **Task Model & Migration**
  - Fields: description, deadline, priority, is_finished, time_finished, from_user_id, to_user_id
  - Relationships: belongsTo(User)
  - Automatically tenant-aware through database switching

- [x] **Team Model & Migration**
  - Fields: name, schedule_precedence
  - Relationships: belongsToMany(User)
  - Automatically tenant-aware through database switching

#### 1.2 Update Existing Models (All Tenant-Aware) ✅ **COMPLETED**
- [x] **Update User Model**
  - Add fields: full_name, is_active, schedule_nick
  - Add relationships: hasMany(Order, ManDay, Task), belongsToMany(Team)
  - Add methods: hasRight(), hasRole()
  - Automatically tenant-aware through database switching
  - Tenant-specific user management

- [x] **Update Project Model**
  - Add relationships: hasMany(Order)
  - Automatically tenant-aware through database switching

#### 1.3 Create Pivot Tables (All Tenant-Aware) ✅ **COMPLETED**
- [x] **article_order table** (Article-Order many-to-many)
- [x] **team_user table** (Team-User many-to-many)
- [x] **right_role table** (Right-Role many-to-many)
- [x] **tenant_user table** (Tenant-User many-to-many for tenant access)

### Phase 2: Filament Resources (Week 4-5)

#### 2.1 Core Resources (All Tenant-Aware) ✅ **COMPLETED**
- [x] **OrderResource**
  - Table with columns: number, client, place, install_date, status, price_to_customer, price_to_supplier
  - Actions: create, edit, delete, duplicate, view info modal
  - Filters: category, status, date range, client (tenant-scoped)
  - Bulk actions: update status, export
  - Tenant isolation: automatic tenant filtering in all queries

- [x] **ClientResource**
  - Table with columns: name, email, phone, company, orders_count
  - Actions: create, edit, delete
  - Filters: company, has_orders (tenant-scoped)
  - Tenant isolation: only show clients for current tenant

- [x] **CompanyResource**
  - Table with columns: name, address, vat, type (supplier/client)
  - Actions: create, edit, delete
  - Filters: type (tenant-scoped)
  - Tenant isolation: only show companies for current tenant

- [x] **ArticleResource**
  - Table with columns: code, designation, project, component, category, unit, quantity, dimensions
  - Actions: create, edit, delete, view details
  - Filters: project, component, category, dimensions (tenant-scoped)
  - Tenant isolation: only show articles for current tenant

#### 2.2 Document Management (Tenant-Aware) ✅ **COMPLETED**
- [x] **DocumentResource**
  - Table with columns: file_name, description, price, date, type, order, category
  - Actions: upload, download, edit, delete, change category
  - Filters: type, category, date range, order (tenant-scoped)
  - Tenant isolation: only show documents for current tenant
  - Tenant-specific file storage paths

- [x] **DocumentCategoryResource**
  - Table with columns: name, type, attributes
  - Actions: create, edit, delete, manage roles
  - Tenant isolation: only show categories for current tenant

#### 2.3 Schedule Management (Tenant-Aware) ✅ **COMPLETED**
- [x] **MontageResource**
  - Calendar view for scheduling (tenant-scoped)
  - Table view with filters (tenant-scoped)
  - Actions: create, edit, delete, confirm
  - Tenant isolation: only show montages for current tenant

- [x] **ManDayResource**
  - Table with columns: user, date, is_vacation, is_medical, price
  - Actions: create, edit, delete, bulk update
  - Tenant isolation: only show man-days for current tenant

- [x] **TaskResource**
  - Table with columns: description, deadline, priority, status, from_user, to_user
  - Actions: create, edit, delete, mark complete
  - Filters: status, user, deadline (tenant-scoped)
  - Tenant isolation: only show tasks for current tenant

### Phase 3: Advanced Features (Week 6-7)

#### 3.1 Custom Pages (Tenant-Aware)
- [ ] **Dashboard Page**
  - Order statistics (tenant-scoped)
  - Recent orders (tenant-scoped)
  - Pending tasks (tenant-scoped)
  - Financial summary (tenant-scoped)
  - Tenant-specific widgets and metrics

- [ ] **Schedule Page**
  - Weekly/monthly calendar view (tenant-scoped)
  - Drag & drop scheduling (tenant-scoped)
  - Team management (tenant-scoped)
  - Tenant-specific schedule configurations

- [ ] **Reports Page**
  - Financial reports (tenant-scoped)
  - User performance (tenant-scoped)
  - Order analytics (tenant-scoped)
  - Export functionality (tenant-scoped)
  - Tenant-specific report templates

#### 3.2 Custom Components (Tenant-Aware)
- [ ] **Order Info Modal**
  - Detailed order information (tenant-scoped)
  - Articles list (tenant-scoped)
  - Documents list (tenant-scoped)
  - Notes section (tenant-scoped)
  - Tenant-specific order configurations

- [ ] **Article Management Modal**
  - Add/remove articles from orders (tenant-scoped)
  - Update quantities and prices (tenant-scoped)
  - Track status (requested, confirmed, delivered)
  - Tenant-specific article categories

- [ ] **Document Upload Component**
  - Drag & drop file upload (tenant-specific storage)
  - Bulk image upload for orders (tenant-specific storage)
  - File preview and management (tenant-scoped)

### Phase 4: Business Logic Implementation (Week 8-9)

#### 4.1 Order Management Logic (Tenant-Aware)
- [ ] **Order Status Workflow**
  - Implement status progression: requested → confirmed → delivered → finished
  - Automatic calculations for budget and montage time (tenant-scoped)
  - Article checkmark management (tenant-scoped)

- [ ] **Financial Calculations**
  - Price calculations (customer/supplier) (tenant-scoped)
  - Balance calculations (tenant-scoped)
  - Debt tracking (tenant-scoped)
  - Payment status (tenant-scoped)

#### 4.2 Schedule Management Logic (Tenant-Aware)
- [ ] **Montage Scheduling**
  - Time allocation logic (tenant-scoped)
  - Conflict detection (tenant-scoped)
  - Team assignment (tenant-scoped)
  - Confirmation workflow (tenant-scoped)

- [ ] **ManDay Management**
  - Vacation tracking (tenant-scoped)
  - Medical leave tracking (tenant-scoped)
  - Salary calculations (tenant-scoped)
  - Performance metrics (tenant-scoped)

#### 4.3 Document Management Logic (Tenant-Aware)
- [ ] **File Management**
  - File upload and storage (tenant-specific paths)
  - Thumbnail generation (tenant-specific storage)
  - File categorization (tenant-scoped)
  - Access control (tenant-scoped)

### Phase 5: User Interface & UX (Week 10-11)

#### 5.1 Custom Filament Themes (Tenant-Aware)
- [ ] **Order Management Interface**
  - Table with inline editing (tenant-scoped)
  - Status indicators (tenant-scoped)
  - Quick actions (tenant-scoped)
  - Advanced filtering (tenant-scoped)

- [ ] **Schedule Interface**
  - Calendar view (tenant-scoped)
  - Drag & drop functionality (tenant-scoped)
  - Team management (tenant-scoped)
  - Time tracking (tenant-scoped)

#### 5.2 Mobile Responsiveness (Tenant-Aware)
- [ ] **Responsive Design**
  - Mobile-friendly tables (tenant-scoped)
  - Touch-friendly actions (tenant-scoped)
  - Optimized forms (tenant-scoped)

### Phase 6: Permissions & Security (Week 12)

#### 6.1 Role-Based Access Control (Tenant-Aware)
- [ ] **Define Roles (Per Tenant)**
  - Admin: Full access within tenant
  - Manager: Order and schedule management within tenant
  - Installer: Schedule and task management within tenant
  - Seller: Order creation and client management within tenant

- [ ] **Permission System (Tenant-Isolated)**
  - Resource-level permissions (tenant-scoped)
  - Action-level permissions (tenant-scoped)
  - Data-level permissions (users can only see their own data within tenant)
  - Cross-tenant access prevention

#### 6.2 Security Features (Tenant-Aware)
- [ ] **Data Protection**
  - Input validation (tenant-scoped)
  - File upload security (tenant-specific storage)
  - SQL injection prevention
  - XSS protection
  - Cross-tenant data leakage prevention

### Phase 7: Testing & Optimization (Week 13)

#### 7.1 Testing (Tenant-Aware)
- [ ] **Unit Tests**
  - Model tests (tenant-scoped)
  - Business logic tests (tenant-scoped)
  - Calculation tests (tenant-scoped)

- [ ] **Feature Tests**
  - Order workflow tests (tenant-scoped)
  - Permission tests (tenant-scoped)
  - File upload tests (tenant-scoped)
  - Tenant switching tests
  - Cross-tenant access prevention tests

#### 7.2 Performance Optimization (Tenant-Aware)
- [ ] **Database Optimization**
  - Index optimization (tenant-aware)
  - Query optimization (tenant-scoped)
  - Eager loading (tenant-scoped)
  - Tenant-specific query optimization

- [ ] **Caching**
  - Query result caching (tenant-scoped)
  - View caching (tenant-scoped)
  - Configuration caching (tenant-scoped)
  - Tenant-specific cache keys

### Phase 8: Deployment & Documentation (Week 14)

#### 8.1 Deployment (Multi-Tenant)
- [ ] **Production Setup**
  - Environment configuration (multi-tenant)
  - Database migration (tenant-aware)
  - File storage setup (tenant-specific)
  - Backup procedures (tenant-isolated)
  - Tenant provisioning system

#### 8.2 Documentation (Multi-Tenant)
- [ ] **User Documentation**
  - User manual (tenant-specific)
  - Feature guides (tenant-specific)
  - Video tutorials (tenant-specific)
  - Tenant onboarding guide

- [ ] **Technical Documentation**
  - API documentation (tenant-aware)
  - Database schema (multi-tenant)
  - Deployment guide (multi-tenant)
  - Tenant management guide

## Technical Considerations

### Dependencies to Add
```json
{
  "barryvdh/laravel-dompdf": "^2.0",
  "intervention/image": "^3.0",
  "spatie/laravel-medialibrary": "^11.0",
  "filament/spatie-laravel-media-library-plugin": "^3.0",
  "stancl/tenancy": "^3.0"
}
```

### Key Filament Features to Use
- Custom table columns with inline editing
- Custom actions and bulk actions
- Custom pages and widgets
- Custom form components
- Custom table filters
- Custom table views (calendar, kanban)

### Database Considerations (Multi-Tenant)
- Use proper indexing for performance (tenant databases)
- Implement soft deletes where appropriate (tenant-scoped)
- Use JSON columns for flexible data storage (tenant-specific)
- Implement proper foreign key constraints (tenant-aware)
- Complete database isolation per tenant
- Tenant-specific database configurations

### Security Considerations (Multi-Tenant)
- Implement proper authorization policies (tenant-scoped)
- Use Filament's built-in security features (tenant-aware)
- Implement file upload validation (tenant-specific storage)
- Use proper CSRF protection
- Complete database isolation prevents cross-tenant access
- Database switching security validation
- Tenant-specific security configurations

## Success Metrics (Multi-Tenant)
- [ ] All old version features replicated (tenant-aware)
- [ ] Performance equal or better than old version (tenant-scoped)
- [ ] User acceptance testing passed (multi-tenant)
- [ ] Security audit passed (database isolation verified)
- [ ] Documentation complete (tenant-specific)
- [ ] Training materials ready (multi-tenant)
- [ ] Complete database isolation verified
- [ ] Database switching functionality tested
- [ ] Tenant-specific configurations working

## Timeline Summary (Updated for Multi-Tenancy)
- **Week 1**: ✅ Multi-tenancy foundation and setup - **COMPLETED**
- **Weeks 2-3**: ✅ Database schema and models (tenant-aware) - **COMPLETED**
- **Weeks 4-5**: ✅ Basic Filament resources (tenant-scoped) - **COMPLETED** (8/8 resources)
- **Weeks 6-7**: Advanced features and custom pages (tenant-aware)
- **Weeks 8-9**: Business logic implementation (tenant-scoped)
- **Weeks 10-11**: UI/UX improvements (tenant-specific)
- **Week 12**: Permissions and security (tenant-isolated)
- **Week 13**: Testing and optimization (multi-tenant)
- **Week 14**: Deployment and documentation (multi-tenant)

**Current Progress**: 3 weeks ahead of schedule! 🚀
**Next Priority**: Advanced features and custom pages (Dashboard, Schedule, Reports)
Total estimated time: 14 weeks (3.5+ months)
