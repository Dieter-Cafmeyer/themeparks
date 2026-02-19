<script setup>
    import { usePage } from '@inertiajs/vue3'

    const page = usePage()
    const user = page.props.auth.user
    const t = (key) => page.props.translations?.[key] ?? key;
</script>

<template >
    <aside v-if="user.is_admin" >
        <nav>
            <ul>
                <li>
                    <Link :href="route('dashboard')"><i class="fas fa-user"></i> {{ t('my_account') }}</Link>
                </li>
                
                <li v-if="user.is_admin" >
                    <Link :href="route('dashboard.parks')"><img src="../../../../assets/coaster.svg" alt=""> {{ t('parks') }}</Link>
                </li>

                <li v-if="user.is_admin" >
                    <Link :href="route('dashboard.users')"><i class="fas fa-users"></i> {{ t('users') }}</Link>
                </li>
            </ul>
        </nav>
    </aside>
</template>