<template>
    <app-layout>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-4">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-new-search-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-new-search" type="button" role="tab" aria-controls="nav-home"
                                    aria-selected="true">New Search
                            </button>
                            <button class="nav-link" id="nav-history-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-history" type="button" role="tab" aria-controls="nav-profile"
                                    aria-selected="false">History
                            </button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-new-search" role="tabpanel"
                             aria-labelledby="nav-new-search-tab">
                            <form method="get" class="mt-3" @submit.prevent="getRates">
                                <div class="mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" placeholder="Start Date"
                                           v-model="start_date">
                                    <span v-if="errors.start_date" class="text-danger">{{ errors.start_date[0] }}</span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" placeholder="End Date" v-model="end_date">
                                    <span v-if="errors.end_date" class="text-danger">{{ errors.end_date[0] }}</span>
                                </div>
                                <label class="form-label">Base Currency</label>
                                <select class="form-select" aria-label="Default select example" v-model="base">
                                    <option selected value="" disabled>--- Select Base Currency ---</option>
                                    <option v-for="symbol in symbols" :key="symbol" :value="symbol.symbol">
                                        {{ symbol.symbol }} - {{
                                            symbol.country_name
                                        }}
                                    </option>
                                </select>
                                <span v-if="errors.base" class="text-danger">{{ errors.base[0] }}</span>
                                <input type="submit" class="btn btn-primary mt-4" value="Submit" :disabled="isLoading">
                            </form>
                        </div>
                        <div class="tab-pane fade" id="nav-history" role="tabpanel" aria-labelledby="nav-history-tab">
                            <div class="table-responsive mt-3" >
                                <table class="table" style="max-height: 600px; overflow: scroll;"   >
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Base</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(history,index) in ratesHistory" :key="history.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ history.start_date }}</td>
                                        <td>{{ history.end_date }}</td>
                                        <td>{{ history.base }}</td>
                                        <td>
                                            <button class="btn btn-link"
                                                    @click="getRatesFromHistory(history.id)">View
                                            </button>
                                            <button class="btn btn-link"
                                                    @click="deleteRatesHistory(history.id)"><i
                                                class="bi bi-clipboard"></i>Delete
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="" v-if="ratesVisible">
                        <div class="d-flex justify-content-end align-items-center">
                            <button class="btn btn-success" @click.prevent="saveRatesHistory">Save Search</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td>Date</td>
                                    <td v-for="symbol in symbols" :key="symbol">{{ symbol.symbol }}</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(value, key) in rates" :key="key">
                                    <td>{{ key }}</td>
                                    <td v-for="(rate, symbol) in value" :key="symbol">{{
                                            parseFloat(rate).toFixed(2)
                                        }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </app-layout>
</template>

<script setup>
import {Link, useForm, usePage} from '@inertiajs/inertia-vue3';
import AppLayout from '../Components/Layouts/AppLayout';
import {onMounted, ref} from "vue";
import {Inertia} from "@inertiajs/inertia";
import axios from "axios";
import {start} from "@popperjs/core";

const start_date = ref('');
const end_date = ref('');
const base = ref('');
const Homerates = ref([]);
const errors = ref({});
const rates = ref([]);
const ratesVisible = ref(false);
const isLoading = ref(false);

const props = defineProps({
    symbols: Array,
    ratesHistory: Array,
});

function validate() {
    if (start_date.value == '') {
        errors.value = {'start_date': ['Start date is required']};
        return false;
    } else if (end_date.value == '') {
        errors.value = {'end_date': ['End date is required']};
        return false;
    } else if (base.value == '') {
        errors.value = {'base': ['Base currency is requiredd']};
        return false;
    }
    return true;
}

function getRates() {
    if (validate()) {
        isLoading.value = true;
        axios.get(route('get_exchange_rates'), {
            params: {
                start_date: start_date.value,
                end_date: end_date.value,
                base: base.value,
            }
        }).then(response => {
            rates.value = response.data;
            ratesVisible.value = true;
            isLoading.value = false;
        }).catch(error => {
            isLoading.value = false;
            ratesVisible.value = false;
            if (error.response.status == 422) {
                errors.value = error.response.data.errors;
                setTimeout(function () {
                    errors.value = {};
                }, 3000)
            } else {
                alert(error.response.data.message);
            }
        });
    }
}

function getRatesFromHistory(id) {
    isLoading.value = true;
    axios.get(route('get_exchange_rates_from_history', id)).then(response => {
        isLoading.value = false;
        rates.value = response.data;
        ratesVisible.value = true;
    }).catch(error => {
        isLoading.value = false;
        ratesVisible.value = false;
        console.log(error);
        if (error.response.status == 422) {
            errors.value = error.response.data.errors;
            setTimeout(function () {
                errors.value = {};
            }, 3000)
        } else {
            alert(error.response.data.message);
        }
    });
}

function deleteRatesHistory(id) {
    if (confirm('Do you really want to delete this entry?')) {
        Inertia.post(route('delete_rates_history', id), {}, {
            preserveState: true,
            onSuccess: () => {
                alert(usePage().props.value.flash.message);
            }
        });
    }
}

function saveRatesHistory() {
    Inertia.post(route('save_rates_history'), {
        start_date: start_date.value,
        end_date: end_date.value,
        base: base.value,
    }, {
        preserveState: true,
        onSuccess: () => {
            alert(usePage().props.value.flash.message);
        }
    })
}

</script>

