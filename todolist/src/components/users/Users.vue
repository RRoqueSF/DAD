<script setup>
import axios from "axios";
import { ref, watchEffect, onMounted } from "vue";
import { useRouter } from "vue-router";
import { Bootstrap5Pagination } from "laravel-vue-pagination";
import { useToast } from "vue-toastification";
import UserTable from "./UserTable.vue"; // Replace with your user table component

const toast = useToast();
const router = useRouter();

// Data
const users = ref([]);
const paginationData = ref({});
const filterByType = ref(null);
const filterByBlocked = ref(null);
const orderBy = ref(null);
const totalUsers = ref(null);

// Fetch users with filters and pagination
const loadUsers = async (page = 1) => {
  try {
    const response = await axios.get("/users", {
      params: {
        page: page,
        type: filterByType.value,
        blocked: filterByBlocked.value,
        order: orderBy.value,
      },
    });
    users.value = response.data.data; // Users list
    paginationData.value = response.data.meta; // Pagination data
    totalUsers.value = response.data.meta.total; // Total users
  } catch (error) {
    console.error(error);
    toast.error("Failed to load users.");
  }
};

watchEffect(() => {
  loadUsers();
});

onMounted(() => {
  loadUsers();
});
</script>
<template>
    <div class="container mt-4">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Users</h3>
        <h5>Total: <span class="text-primary">{{ totalUsers }}</span></h5>
      </div>
  
      <!-- Filters -->
      <div class="card p-3 mb-4">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="selectType" class="form-label fw-semibold">Filter by Type:</label>
            <select class="form-select" id="selectType" v-model="filterByType">
              <option :value="null">All</option>
              <option value="A">Admin</option>
              <option value="P">Player</option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="selectBlocked" class="form-label fw-semibold">Filter by Blocked:</label>
            <select class="form-select" id="selectBlocked" v-model="filterByBlocked">
              <option :value="null">All</option>
              <option value="1">Blocked</option>
              <option value="0">Not Blocked</option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="selectOrderBy" class="form-label fw-semibold">Order by:</label>
            <select class="form-select" id="selectOrderBy" v-model="orderBy">
              <option :value="null">Default</option>
              <option value="desc">Recent</option>
              <option value="asc">Oldest</option>
            </select>
          </div>
        </div>
      </div>
      <Bootstrap5Pagination
    :data="paginationData"
    @pagination-change-page="loadUsers"
    :limit="2"
  ></Bootstrap5Pagination>
      <!-- User Table -->
      <div class="table-responsive pb-50">
        <user-table
          :users="users"
          :showId="true"
          :showDates="true"
        ></user-table>
      </div>
    </div>
  




  </template>

