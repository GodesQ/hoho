<ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ preg_match('/admin\/dashboard/', Request::path()) ? 'active' : null }}">
        <a href="{{ route('admin.dashboard') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Dashboard">Dashboard</div>
        </a>
    </li>

    @canany(['view_users_list', 'view_organizations_list', 'view_admins_list'])
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Accounts</span>
        </li>
    @endcanany

    @auth('admin')
        @can('view_users_list')
            <li class="menu-item {{ preg_match('/admin\/users/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.users.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user-circle"></i>
                    <div data-i18n="Guests">Guests</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @can('view_organizations_list')
            <li class="menu-item {{ preg_match('/admin\/organizations/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.organizations.list') }}" class="menu-link">
                    <i class='bx bx-globe menu-icon'></i>
                    <div data-i18n="Organizations">Organizations</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @can('view_admins_list')
            <li class="menu-item {{ preg_match('/admin\/admins/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.admins.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user-circle"></i>
                    <div data-i18n="Admins">Admins</div>
                </a>
            </li>
        @endcan
    @endauth

    <li class="menu-item {{ preg_match('/admin\/merchants/', Request::path()) ? 'active open' : null }}">
        @auth('admin')
            @canany(['view_merchant_stores_list', 'view_merchant_restaurants_list', 'view_merchant_hotels_list',
                'view_merchant_tour_providers_list'])
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-cube-alt"></i>
                    <div data-i18n="Merchants">Merchants</div>
                </a>
            @endcanany
        @endauth

        <ul class="menu-sub">
            @auth('admin')
                @can('view_merchant_stores_list')
                    <li class="menu-item {{ preg_match('/admin\/merchants\/stores/', Request::path()) ? 'active' : null }}">
                        <a href="{{ route('admin.merchants.stores.list') }}" class="menu-link">
                            <div data-i18n="Stores">Stores</div>
                        </a>
                    </li>
                @endcan
            @endauth

            @auth('admin')
                @can('view_merchant_restaurants_list')
                    <li
                        class="menu-item {{ preg_match('/admin\/merchants\/restaurants/', Request::path()) ? 'active' : null }}">
                        <a href="{{ route('admin.merchants.restaurants.list') }}" class="menu-link">
                            <div data-i18n="Restaurants">Restaurants</div>
                        </a>
                    </li>
                @endcan
            @endauth

            @auth('admin')
                @can('view_merchant_hotels_list')
                    <li class="menu-item {{ preg_match('/admin\/merchants\/hotels/', Request::path()) ? 'active' : null }}">
                        <a href="{{ route('admin.merchants.hotels.list') }}" class="menu-link">
                            <div data-i18n="Hotels">Hotels</div>
                        </a>
                    </li>
                @endcan
            @endauth

            @auth('admin')
                @can('view_merchant_tour_providers_list')
                    <li
                        class="menu-item {{ preg_match('/admin\/merchants\/tour_providers/', Request::path()) ? 'active' : null }}">
                        <a href="{{ route('admin.merchants.tour_providers.list') }}" class="menu-link">
                            <div data-i18n="Tour Providers">Tour Providers</div>
                        </a>
                    </li>
                @endcan
            @endauth

        </ul>
    </li>

    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Merchants</span>
    </li>

    <li class="menu-item {{ preg_match('/admin\/products/', Request::path()) ? 'active' : null }}">
        <a href="{{ route('admin.products.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-cube-alt"></i>
            <div data-i18n="Products">Products</div>
        </a>
    </li>

    <li class="menu-item {{ preg_match('/admin\/rooms/', Request::path()) ? 'active' : null }}">
        <a href="{{ route('admin.rooms.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-cube-alt"></i>
            <div data-i18n="Rooms">Rooms</div>
        </a>
    </li>

    <li class="menu-item {{ preg_match('/admin\/foods/', Request::path()) ? 'active' : null }}">
        <a href="{{ route('admin.foods.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-cube-alt"></i>
            <div data-i18n="Foods">Foods</div>
        </a>
    </li>

    <li class="menu-item {{ preg_match('/admin\/food-categories/', Request::path()) ? 'active' : null }}">
        <a href="{{ route('admin.food_categories.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-cube-alt"></i>
            <div data-i18n="Food Categories">Food Categories</div>
        </a>
    </li>

    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Reservations</span>
    </li>

    <li class="menu-item {{ preg_match('/admin\/tour_reservations/', Request::path()) ? 'active' : null }}">
        <a href="{{ route('admin.tour_reservations.list') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-file"></i>
            <div data-i18n="Tour Reservations">Tour Reservations</div>
        </a>
    </li>

    <li class="menu-item">
        <a href="" class="menu-link">
            <i class="menu-icon tf-icons bx bx-file"></i>
            <div data-i18n="Hotel Reservations">Hotel Reservations</div>
        </a>
    </li>

    <li class="menu-item">
        <a href="" class="menu-link">
            <i class="menu-icon tf-icons bx bx-file"></i>
            <div data-i18n="Restaurant Reservations">Restaurant Reservations</div>
        </a>
    </li>
    

    @auth('admin')
        @canany(['view_tours_list', 'view_attractions_list', 'view_transports_list'])
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Tours</span></li>
        @endcanany
    @endauth

    @auth('admin')
        @can('view_tours_list')
            <!-- Tours -->
            <li class="menu-item {{ preg_match('/admin\/tours/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.tours.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-dock-top"></i>
                    <div data-i18n="Tours">Tours</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @can('view_tour_badges_list')
            <li class="menu-item {{ preg_match('/admin\/tour_badges/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.tour_badges.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-badge"></i>
                    <div data-i18n="Tour Badges">Tour Badges</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @can('view_attractions_list')
            <!-- Attractions -->
            <li class="menu-item {{ preg_match('/admin\/attractions/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.attractions.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-dock-bottom"></i>
                    <div data-i18n="Attractions">Attractions</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @can('view_transports_list')
            <!-- Transports -->
            <li class="menu-item {{ preg_match('/admin\/transports/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.transports.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-bus"></i>
                    <div data-i18n="Transports">Transports</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @can('view_ticket_passes_list')
            <li class="menu-item {{ preg_match('/admin\/ticket_passes/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.ticket_passes.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-box"></i>
                    <div data-i18n="Ticket Passes">Ticket Passes</div>
                </a>
            </li>
        @endcan
    @endauth

    {{-- @auth('admin')
        @can('view_tour_reservations_list')
            <li class="menu-item {{ preg_match('/admin\/tour_reservations/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.tour_reservations.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div data-i18n="Tour Reservations">Tour Reservations</div>
                </a>
            </li>
        @endcan
    @endauth --}}

    @auth('admin')
        @can('view_carts_list')
            <li class="menu-item {{ preg_match('/admin\/carts/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.carts.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-cart"></i>
                    <div data-i18n="Carts">Carts</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @canany(['view_transactions_list', 'view_sales_report'])
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Transactions &
                    Reports</span></li>
        @endcanany
    @endauth

    @auth('admin')
        @can('view_transactions_list')
            <li class="menu-item {{ preg_match('/admin\/transactions/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.transactions.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-money"></i>
                    <div data-i18n="Booking Transactions">Booking Transactions</div>
                </a>
            </li>
        @endcan
    @endauth

    <li class="menu-item {{ preg_match('/admin\/reports/', Request::path()) ? 'active open' : null }}">
        @auth('admin')
            @canany(['view_sales_report'])
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-chart"></i>
                    <div data-i18n="Reports">Reports</div>
                </a>
            @endcanany
        @endauth


        <ul class="menu-sub">
            @auth('admin')
                @can('view_sales_report')
                    <li
                        class="menu-item {{ preg_match('/admin\/reports\/sales_report/', Request::path()) ? 'active' : null }}">
                        <a href="{{ route('admin.reports.sales_report') }}" class="menu-link">
                            <div data-i18n="Sales Report">Sales Report</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ preg_match('/admin\/reports\/tour_reservations_report/', Request::path()) ? 'active' : null }}">
                        <a href="{{ route('admin.reports.tour_reservations_report') }}" class="menu-link">
                            <div data-i18n="Tour Reservations Report">Tour Reservations Report</div>
                        </a>
                    </li>
                @endcan
            @endauth
            {{-- <li
                class="menu-item {{ preg_match('/admin\/reports/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.reports.user_demographics') }}" class="menu-link">
                    <div data-i18n="Users Demographics">Users Demographics</div>
                </a>
            </li> --}}
        </ul>
    </li>


    @auth('admin')
        @canany(['view_interests_list', 'view_referrals_list', 'view_promo_codes_list', 'view_product_categories_list'])
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Others</span></li>
        @endcanany
    @endauth


    @auth('admin')
        @can('view_interests_list')
            <li class="menu-item {{ preg_match('/admin\/interests/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.interests.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-bulb"></i>
                    <div data-i18n="Interests">Interests</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @can('view_referrals_list')
            <li class="menu-item {{ preg_match('/admin\/referrals/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.referrals.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-gift"></i>
                    <div data-i18n="Referrals">Referrals</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @can('view_promo_codes_list')
            <li class="menu-item {{ preg_match('/admin\/promo_codes/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.promo_codes.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-gift"></i>
                    <div data-i18n="Promo Codes">Promo Codes</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @can('view_product_categories_list')
            <li class="menu-item {{ preg_match('/admin\/product_categories/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.product_categories.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-box"></i>
                    <div data-i18n="Product Categories">Product Categories</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @can('view_announcements_list')
            <li class="menu-item {{ preg_match('/admin\/announcements/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.announcements.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-message"></i>
                    <div data-i18n="Announcements">Announcements</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @can('view_unavailable_dates_list')
            <li class="menu-item {{ preg_match('/admin\/unavailable_dates/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.unavailable_dates.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-calendar"></i>
                    <div data-i18n="Unavailable Dates">Unavailable Dates</div>
                </a>
            </li>
        @endcan
    @endauth

    <!-- Roles & Permissions -->
    @auth('admin')
        @canany(['view_roles_list', 'view_permissions_list'])
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Roles &
                    Permissions</span></li>
        @endcanany
    @endauth

    @auth('admin')
        @can('view_roles_list')
            <li class="menu-item {{ preg_match('/admin\/roles/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.roles.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user-check"></i>
                    <div data-i18n="Roles">Roles</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @can('view_permissions_list')
            <li class="menu-item {{ preg_match('/admin\/permissions/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('admin.permissions.list') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user-check"></i>
                    <div data-i18n="Permissions">Permissions</div>
                </a>
            </li>
        @endcan
    @endauth

    @auth('admin')
        @canany(['view_merchant_form'])
            <li class="menu-header small text-uppercase"><span class="menu-header-text">My Merchant</span></li>
        @endcanany
    @endauth

    @auth('admin')
        @can('view_merchant_form')
            <?php
            if (auth('admin')->user()->role == 'merchant_store_admin' || auth('admin')->user()->role == 'merchant_store_employee') {
                $type = 'store';
            }
            
            if (auth('admin')->user()->role == 'merchant_restaurant_admin' || auth('admin')->user()->role == 'merchant_restaurant_employee') {
                $type = 'restaurant';
            }
            
            if (auth('admin')->user()->role == 'merchant_hotel_admin' || auth('admin')->user()->role == 'merchant_hotel_employee') {
                $type = 'hotel';
            }
            
            if (auth('admin')->user()->role == 'tour_operator_admin' || auth('admin')->user()->role == 'tour_operator_employee') {
                $type = 'tour_provider';
            }
            ?>

            <li class="menu-item {{ preg_match('/merchant_form/', Request::path()) ? 'active' : null }}">
                <a href="{{ route('merchant_form', $type) }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div data-i18n="Merchant Profile">Merchant Profile</div>
                </a>
            </li>
        @endcan
    @endauth

</ul>
