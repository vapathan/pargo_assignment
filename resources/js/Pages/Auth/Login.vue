<template>
    <app-layout>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div v-if="$page.props.flash.success" class="text-success">{{$page.props.flash.success}}</div>
                <form method="post" @submit.prevent="submit">
                    <h2 class="text-center">Sign In</h2>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" name="email" id="email" v-model="form.email"/>
                        <span class="text-danger" v-if="errors.email">{{errors.email}}</span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" id="password"
                               v-model="form.password"/>
                    </div>
                    <input type="submit" class="btn btn-primary btn-block mt-3" value="Login"/>
                    <Link :href="route('register')" class="btn btn-secondary btn-block mt-3 mx-3">Register</Link>
                </form>
            </div>
        </div>
    </app-layout>
</template>

<script setup>
import {Inertia} from "@inertiajs/inertia";
import {reactive} from "vue";
import AppLayout from '../../Components/Layouts/AppLayout';
import {Link} from '@inertiajs/inertia-vue3';
const form = reactive({
    email: null,
    password: null,
});

const props = defineProps({
    errors:Object
})
function submit() {
    Inertia.post(route('login'), form);
}
</script>
