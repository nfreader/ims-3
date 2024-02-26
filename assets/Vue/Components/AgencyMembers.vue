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
                <td class="text-center position-relative"><a href="#" class="stretched-link" @click="addUser(u.id)"><i class="fa-solid fa-circle-plus text-success"></i></a></td>
                <td>{{ u.firstName }}</td>
                <td>{{ u.lastName }}</td>
                <td>{{ u.email }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    data() {
        return {
            currentAgency: undefined,
            users: []
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
                    console.log(u)
                    if(!u.agencyList.includes(this.currentAgency)){
                    this.users.push(u)
                }
                });

            })
        },
        addUser(id) {
            console.log(id)
            const data = new FormData;
            data.append('target', id)
            data.append('agency', this.currentAgency)
            fetch(`/manage/users/${id}/agencies`, {
                body: data,
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                },
            }).then((res) => res.json())
            .then((res) => {
                console.log(res)
            })
        }
    },
    mounted() {
        this.getUserList()
        this.currentAgency = document.getElementById('addMemberToAgency').dataset.currentAgency
        console.log(this.currentAgency)
    }
}
</script>