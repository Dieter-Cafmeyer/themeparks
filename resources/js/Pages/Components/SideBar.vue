<template>
    <aside>
        <nav>
            <ul>
                <li>
                    <Link :href="route('dashboard')"><i class="fas fa-user"></i> Account</Link>
                </li>

                <li>
                    <Link :href="route('dashboard.jobs')"><i class="fas fa-briefcase"></i> Jobs</Link>
                </li>

                <li>
                    <Link :href="route('dashboard.users')" v-if="$page.props.auth.user?.is_admin"><i class="fas fa-users"></i> Users</Link>
                </li>

                <li>
                    <Link :href="route('logout')" method="post" as="button"><i class="fas fa-power-off"></i> Logout</Link>
                </li>
            </ul>
        </nav>
    </aside>
</template>