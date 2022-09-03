<template>
    <header class="mt-2 mb-2 container-fluid">
        <div class="d-flex justify-content-between">
            <h3 class="text-center">Exchange Rate API Version 1.0</h3>
            <div v-if="$page.props.user.id">
                <Link :href="route('logout')" method="post">Logout</Link>
            </div>
        </div>
        <hr>
    </header>
    <section class="container-fluid">
        <slot></slot>
    </section>
</template>

<script setup>
import {Link} from '@inertiajs/inertia-vue3'
</script>

