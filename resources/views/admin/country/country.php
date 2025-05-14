
<?php
$header_title = "Country List";
// Include layout (or other necessary files)
include __DIR__ . '/../layouts/app.php';

include __DIR__ . '/../sidebar/sidebar.php';

if (file_exists($sidebarPath)) {
    include $sidebarPath;
}

?>


<div class="container mt-5" id="countryApp">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Country List</h1>
        <div>
            <input type="text" id="searchInput" class="form-control form-control-sm"
                placeholder="Search countries..." v-model="searchQuery">
            <a href="/admin/country/create" class="btn btn-success mt-2">Add New Country</a>
        </div>
    </div>

    <?php if (!empty($countries)): ?>
    <table class="table table-striped" id="countriesTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Actions</th>
                <th>States</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="country in filteredCountries" :key="country.id">
                <td>{{ country.id }}</td>
                <td>{{ country.name }}</td>
                <td>
                    <span :class="{'badge bg-warning': country.status == 0, 'badge bg-success': country.status == 1}">
                        {{ country.status == 0 ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        :data-bs-target="'#editCountryModal-' + country.id">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" @click="confirmDelete(country.id)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
                <td>
                    <a :href="'/admin/country/state/' + country.id" class="btn btn-sm btn-info">
                        Manage States
                    </a>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="modal fade" v-for="country in countries" :key="'editModal-' + country.id" :id="'editCountryModal-' + country.id"
        tabindex="-1" :aria-labelledby="'editCountryModalLabel-' + country.id" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" :id="'editCountryModalLabel-' + country.id">Edit Country</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form :action="'/admin/country/update/' + country.id" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label :for="'name-' + country.id" class="form-label">Country Name:</label>
                            <input type="text" class="form-control" :id="'name-' + country.id" name="name"
                                :value="country.name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status"
                                    :id="'pending-' + country.id" value="1" :checked="country.status == 1">
                                <label class="form-check-label" :for="'pending-' + country.id">
                                    Pending
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status"
                                    :id="'active-' + country.id" value="0" :checked="country.status == 0">
                                <label class="form-check-label" :for="'active-' + country.id">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php else: ?>
    <p class="alert alert-info">No countries found.</p>
    <?php endif; ?>
</div>

<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            countries: <?php echo json_encode($countries); ?>,
            searchQuery: ''
        };
    },
    computed: {
        filteredCountries() {
            return this.countries.filter(country => {
                return country.name.toLowerCase().includes(this.searchQuery.toLowerCase());
            });
        }
    },
    methods: {
        confirmDelete(countryId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // You would typically make an AJAX request here to delete the country
                    // and then update the 'countries' array in your Vue data.
                    window.location.href = `/admin/country/delete/${countryId}`;
                }
            });
        }
    },
    mounted() {
        console.log('Vue app mounted!');
    }
}).mount('#countryApp');
</script>