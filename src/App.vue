
<script setup>
import { ref, computed, onMounted} from 'vue'

// Get booking data from database
const bookings = ref([])

// Values controlled by the user
const search = ref('')
const customer = ref('')
const destination = ref('')
const selectedStatus = ref('All')
const editingId = ref(null)

//function of geting database data
async function loadBookings() {
  try {
    const response = await fetch(
      'http://localhost:8000/bookings.php'
    )

    if (!response.ok) {
      throw new Error('Failed to load bookings')
    }

    bookings.value = await response.json()
  } catch (error) {
    console.error('Error loading bookings:', error)
  }
}
onMounted(() => {
  loadBookings()
})

// Filter bookings when the search changes


const filteredBookings = computed(() => {
  const keyword = search.value.trim().toLowerCase()

  return bookings.value.filter(booking => {
    const matchesSearch =
      String(booking.id).includes(keyword) ||
      booking.customer.toLowerCase().includes(keyword)

    const matchesStatus =
      selectedStatus.value === 'All' ||
      booking.status === selectedStatus.value

    return matchesSearch && matchesStatus
  })
})



// Calculate the number of bookings
const totalBookings = computed(() => bookings.value.length)
const confirmedBookings = computed(() => 
  bookings.value.filter(booking => booking.status === 'Confirmed').length
)


// Add a new booking when the form is submitted

async function addBooking() {
  const name = customer.value.trim()
  const place = destination.value.trim()

  if (!name || !place) return

  const isEditing = editingId.value !== null

  try {
    const response = await fetch(
      'http://localhost:8000/bookings.php',
      {
        method: isEditing ? 'PUT' : 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(
          isEditing
            ? {
                id: editingId.value,
                customer: name,
                destination: place
              }
            : {
                customer: name,
                destination: place
              }
        )
      }
    )

    const result = await response.json()

    if (!response.ok) {
      throw new Error(result.error || 'Failed to save booking')
    }

    await loadBookings()

    editingId.value = null
    customer.value = ''
    destination.value = ''
  } catch (error) {
    console.error('Error saving booking:', error)
    alert(error.message)
  }
}



//Add a delete function

async function deleteBooking(id) {
  if (id === null || id === undefined) return

  try {
    const response = await fetch(
      'http://localhost:8000/bookings.php',
      {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id })
      }
    )

    const result = await response.json()

    if (!response.ok) {
      throw new Error(result.error || 'Failed to delete booking')
    }

    await loadBookings()

    if (editingId.value === id) {
      cancelEdit()
    }
  } catch (error) {
    console.error('Error deleting booking:', error)
    alert(error.message)
  }
}

// Add a edit button
function startEdit(booking) {
  editingId.value = booking.id
  customer.value = booking.customer
  destination.value = booking.destination
}
// Add a cancel function
function cancelEdit() {
  editingId.value = null
  customer.value = ''
  destination.value = ''
}
//confirmed page
async function toggleStatus(id) {
  const booking = bookings.value.find(
    booking => booking.id === id
  )

  if (!booking) return

  const newStatus =
    booking.status === 'Pending'
      ? 'Confirmed'
      : 'Pending'

  try {
    const response = await fetch(
      'http://localhost:8000/bookings.php',
      {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id: id,
          status: newStatus
        })
      }
    )

    const result = await response.json()

    if (!response.ok) {
      throw new Error(result.error || 'Failed to update status')
    }

    await loadBookings()
  } catch (error) {
    console.error('Error updating booking status:', error)
    alert(error.message)
  }
}

</script>

<template>
  <main class="page">
    <h1>Travel Booking Dashboard</h1>
    <p class="subtitle">Manage travel bookings</p>

    <section class="summary">
      <span>Total bookings</span>
      <strong>{{ totalBookings }}</strong>
      <span>Confirmed bookings</span>
      <strong>{{ confirmedBookings }}</strong>
    </section>

    <form class="booking-form" @submit.prevent="addBooking">
      <h2>{{ editingId === null ? 'Add a booking' : 'Edit a booking' }}</h2>

      <input
        v-model="customer"
        placeholder="Customer name"
        aria-label="Customer name"
        required
      />

      <input
        v-model="destination"
        placeholder="Destination"
        aria-label="Destination"
        required
      />

      <div class="form-actions">
        <button type="submit">
          {{ editingId === null ? 'Add booking' : 'Save changes' }}
        </button>

        <button
          v-if="editingId !== null"
          type="button"
          @click="cancelEdit"
        >
          Cancel
        </button>
        <button
          v-if="editingId !== null"
          type="button"
          @click="deleteBooking(editingId)"
        >
          Delete
        </button>
      </div>
    </form>

    <section class="booking-list">
      <h2>Bookings</h2>

      <input
        v-model="search"
        placeholder="Search ID or customer..."
        aria-label="Search bookings"
      />
      <select v-model="selectedStatus" aria-label="Filter by status">
        <option value="All">All statuses</option>
        <option value="Confirmed">Confirmed</option>
        <option value="Pending">Pending</option>
      </select>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Destination</th>
            <th>Status</th>
            <th>Actions</th>
            <th>Change</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="booking in filteredBookings" :key="booking.id">
            <td>{{ booking.id }}</td>
            <td>{{ booking.customer }}</td>
            <td>{{ booking.destination }}</td>
            <td>{{ booking.status }}</td>
            <td>
              

              <button
                type="button"
                @click="toggleStatus(booking.id)"
              >
                {{ booking.status === 'Pending' ? 'Confirm' : 'Set to Pending' }}
              </button>
            </td>
            <td>
              <button type="button" @click="startEdit(booking)">
                Edit
              </button>
            </td>
          </tr>

          <tr v-if="filteredBookings.length === 0">
            <td colspan="4">No bookings found.</td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</template>

<style>
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: Arial, sans-serif;
  color: #1f2937;
  background: #f3f5f8;
}

.page {
  max-width: 1000px;
  margin: 40px auto;
  padding: 24px;
}

h1 {
  margin-bottom: 18px;
}

h2 {
  font-size: 18px;
}

.subtitle {
  color: #64748b;
}

.summary,
.booking-form,
.booking-list {
  margin-top: 24px;
  padding: 24px;
  background: white;
  border-radius: 12px;
}

.summary {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.summary strong {
  font-size: 28px;
}

input {
  width: 100%;
  padding: 12px;
  margin: 8px 0;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
}

button {
  padding: 12px 18px;
  border: none;
  border-radius: 6px;
  background: #16805d;
  color: white;
  cursor: pointer;
}

table {
  width: 100%;
  margin-top: 16px;
  border-collapse: collapse;
  text-align: left;
}

th,
td {
  padding: 14px 10px;
  border-bottom: 1px solid #e5e7eb;
}

@media (max-width: 600px) {
  .page {
    margin: 10px auto;
    padding: 12px;
  }

  .booking-list {
    overflow-x: auto;
  }

  table {
    min-width: 560px;
  }
}
</style>
