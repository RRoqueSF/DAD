<script setup>
import { useAuthStore } from '@/stores/auth'; // Import the auth store

import axios from 'axios'; // Import Axios for API requests
const storeAuth = useAuthStore(); // Access the auth store

defineProps({
  users: {
    type: Array,
    required: true,
  },
});

// Methods for button actions
const deleteClick = (user) =>{
  const response = axios.delete(`/admins/${user.id}`);
  console.log('Delete clicked:', response);
};

const blockClick = async (user) => {
    try {
       
        const newBlockedStatus = user.blocked === 0 ? 1 : 0;

        
        const response = await axios.patch(`/users/${user.id}`, {
            blocked: newBlockedStatus,
        });

        console.log('Block clicked:', response.data);

        
        user.blocked = newBlockedStatus;
    } catch (error) {
        console.error('Error updating blocked status:', error.response?.data || error.message);
    }
};
</script>

<template>
  <table class="table table-striped">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Type</th>
        <th>Blocked</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="user in users" :key="user.id">
        <td>{{ user.id }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ user.type }}</td>
        <td>
          <span
            :class="user.blocked ? 'badge bg-danger' : 'badge bg-success'"
          >
            {{ user.blocked === 1 ? "Yes" : "No" }}
          </span>
        </td>
        <td class="text-end">
          <div class="d-flex justify-content-end gap-2">
            <!-- Hide buttons if the user is the current logged-in user -->
            <template v-if="storeAuth.user.id !== user.id">
              <button
                class="btn btn-sm btn-outline-danger"
                @click="deleteClick(user)"
              >
                <i class="bi bi-trash"></i> Delete
              </button>
              <button
                class="btn btn-sm"
                :class="user.blocked ? 'btn-outline-success' : 'btn-outline-danger'"
                @click="blockClick(user)"
              >
                <i :class="user.blocked ? 'bi bi-unlock' : 'bi bi-lock'"></i>
                {{ user.blocked ? "Unblock" : "Block" }}
              </button>
            </template>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<style scoped>
/* Improved styling for buttons */
.btn {
  font-size: 0.9rem;
  padding: 0.4rem 0.8rem;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.btn-outline-danger {
  color: #dc3545;
  border-color: #dc3545;
}
.btn-outline-danger:hover {
  background-color: #dc3545;
  color: #fff;
}

.btn-outline-success {
  color: #28a745;
  border-color: #28a745;
}
.btn-outline-success:hover {
  background-color: #28a745;
  color: #fff;
}

/* Icons styling for consistency */
.bi {
  margin-right: 0.3rem;
}

/* Style table spacing */
.table-striped tbody tr:nth-of-type(odd) {
  background-color: #f8f9fa;
}

.table {
  border-spacing: 0 0.5rem; /* Adds space between rows */
  border-collapse: separate; /* Ensures spacing is applied */
  margin-bottom: 2rem; /* Adds spacing between table and footer */
}

.table th,
.table td {
  padding: 1rem; /* Adds space inside the cells */
  text-align: left; /* Ensures text is aligned consistently */
  border-bottom: 1px solid #dee2e6; /* Adds horizontal lines between rows */
}

.table-wrapper {
  margin-bottom: 3rem; /* Add space below the table */
}
</style>
