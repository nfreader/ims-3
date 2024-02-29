<template>
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email Address</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="u in users" :data-user-id="u.id">
                <td class="align-middle"><a href="#" class="btn btn-outline-success btn-sm d-block" @click="addUser(u)"><i class="fa-solid fa-circle-plus"></i> Add User</a></td>
                <td class="align-middle">{{ u.firstName }}</td>
                <td class="align-middle">{{ u.lastName }}</td>
                <td class="align-middle">{{ u.email }}</td>
            </tr>
        </tbody>
    </table>
    <div v-if="error" class="fw-bold text-danger p-2">{{ error }}</div>
</template>

<script>
export default {
    data() {
        return {
            users: [],
            currentRole: null,
            error: null
        }
    },
    methods: {
        getUserList() {
            fetch('/manage/users', {
                headers: {
                    "Accept": "application/json",
                },
            }).then((res) => res.json())
            .then((res) => {
                res.users.forEach(u => {
                    this.users.push(u)
                });

            })
        },
        addUser(u) {
            const data = new FormData;
            data.append('target', u.id)
            data.append('role', this.currentRole)
            fetch(`/manage/role/${u.id}/user`, {
                body: data,
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                },
            }).then((res) => res.json())
            .then((res) => {
                if(res.error){
                    this.error = res.error.message
                } 
            })
        }
    },
    mounted() {
        this.getUserList()
        this.currentRole = document.querySelector("#addUserModal").dataset.role
        console.log(this.currentRole)
    }
}
</script>