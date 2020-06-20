<template>
    <v-container fluid fill-height>
        <v-layout align-center justify-center>
            <v-flex xs12 sm8 md6>
                <v-card class="elevation-12 ">
                    <v-toolbar dark class="gradient-project">
                        <v-toolbar-title>Сброс пароля</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form v-model="valid" ref="form" validation>

                            <v-text-field
                                prepend-icon="mdi-lock"
                                name="password"
                                label="Пароль"
                                type="password"
                                v-model="user.password"
                                counter=">6"
                                aria-required="true"
                                :rules="passwordRules"
                                color="#ff9966"
                            />
                            <v-text-field
                                prepend-icon="mdi-lock"
                                name="confirm-password"
                                label="Подтвердите пароль"
                                type="password"
                                v-model="user.password_confirmation"
                                aria-required="true"
                                :rules="confirmPasswordRules"
                                color="#ff9966"
                            />
                            <v-alert type="error" v-if="error">
                                {{ error }}
                            </v-alert>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer/>
                        <v-btn
                            class="gradient-project"
                            :disabled="!valid"
                            v-bind:dark="valid"
                            large
                            @click="onSubmit"
                        >Сброс
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    export default {
        data () {
            return {
                user: {
                    password: '',
                    password_confirmation: ''
                },
                valid: false,
                error: false,
                passwordRules: [
                    v => !!v || 'Заполните пароль!',
                    v => (v && v.length >= 8) || 'Пароль должен быть длинной более 8-ми символов'
                ],
                confirmPasswordRules: [
                    v => !!v || 'Подтвердите пароль!',
                    v => (v && v === this.user.password) || 'Пароли должены совпадать'
                ],
            }
        },
        methods: {
            onSubmit () {
                if(this.$route.query.email && this.$route.query.token) {
                    let user = this.user
                    let resetData = {
                        email: this.$route.query.email,
                        token: this.$route.query.token,
                        password: user.password,
                        password_confirmation: user.password_confirmation,
                    }
                    this.$store.dispatch('resetSave', resetData)
                        .then(() => {
                            this.$router.push('/')
                        })
                        .catch(err => this.$router.push('/'))
                } else {
                    this.$router.push('/')
                }
            }
        },
        mounted() {

        }
    }
</script>
