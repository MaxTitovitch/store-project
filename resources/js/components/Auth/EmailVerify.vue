<template>
    <v-container fluid fill-height>
        <v-layout align-center justify-center>
            <v-flex xs12 sm8 md6>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    export default {
        data () {
            return {
            }
        },
        mounted() {
            if(this.$route.query.id && this.$route.query.token) {
                let verifyData = {
                    id: this.$route.query.id,
                    token: this.$route.query.token,
                }
                this.$store.dispatch('verifySave', verifyData)
                    .then(() => {
                        this.$router.push('/')
                    })
                    .catch(err => this.$router.push('/'))
            } else {
                this.$router.push('/')
            }
        }
    }
</script>
