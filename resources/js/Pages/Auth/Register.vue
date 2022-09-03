<template>
    <app-layout>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <form method="post" @submit.prevent="submit">
                    <h2 class="text-center">Create New Account</h2>
                    <div class="form-group mt-3">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" v-model="form.name"/>
                        <span class="text-danger" v-if="errors.name">{{errors.name}}</span>
                    </div>
                    <div class="form-group mt-3">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" name="email" id="email" v-model="form.email"/>
                        <span class="text-danger" v-if="errors.email">{{errors.email}}</span>
                    </div>
                    <div class="form-group mt-3">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" id="password"
                               v-model="form.password"/>
                        <span class="text-danger" v-if="errors.password">{{errors.password}}</span>
                    </div>
                    <input type="submit" class="btn btn-primary btn-block mt-3" value="Register"/>
                </form>
            </div>
        </div>
    </app-layout>
</template>

<script setup>
import {Inertia} from "@inertiajs/inertia";
import {reactive} from "vue";
import AppLayout from '../../Components/Layouts/AppLayout';
import {Link} from '@inertiajs/inertia-vue3'

const form = reactive({
    name: null,
    email: null,
    password: null,
});

const props = defineProps({
    errors:Object
})

function submit() {
    Inertia.post(route('register'), form);
}

</script>
