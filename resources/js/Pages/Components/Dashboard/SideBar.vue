<template>
    <aside>
        <nav>
            <ul>
                <li>
                    <Link :href="route('dashboard')"><i class="fas fa-user"></i> Account</Link>
                </li>
                
                <li>
                    <Link :href="route('admin.parks')"><img src="../../../../assets/coaster.svg" alt=""> Parks</Link>
                </li>

                <li>
                    <Link :href="route('dashboard.users')" v-if="$page.props.auth.user?.is_admin"><i class="fas fa-users"></i> Users</Link>
                </li>
            </ul>
        </nav>
    </aside>
</template>