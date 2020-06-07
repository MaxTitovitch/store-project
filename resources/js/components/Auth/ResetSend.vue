<template>
    <v-container fluid fill-height>
        <v-layout align-center justify-center>
            <v-flex xs12 sm8 md6>
                <v-card class="elevation-12 ">
                    <v-toolbar dark class="gradient-project">
                        <v-toolbar-title>Вход в систему</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <div  class="green--text text-center">
                            <h2>{{ message }}</h2>
                        </div>
                        <v-form v-model="valid" ref="form" validation>
                            <v-text-field
                                prepend-icon="mdi-account"
                                name="email"
                                label="E-mail для сброса"
                                type="text"
                                v-model="user.email"
                                aria-required="true"
                                :rules="emailRules"
                            />
                            <div v-if="error" class="red--text">
                                {{ error }}
                            </div>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-layout align-center justify-center>
                            <v-flex xs12 sm8 md6>
                                <v-btn
                                    class="gradient-project"
                                    large
                                    @click="onSubmit"
                                    :disabled="!valid"
                                    v-bind:dark="valid"
                                    width="100%"
                                >Отправить письмо
                                </v-btn>
                            </v-flex>
                        </v-layout>
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
                    email: '',
                },
                valid: false,
                error: false,
                message: '',
                emailRules: [
                    v => !!v || 'Заполните E-mail!',
                    v => /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail должен быть валиден'
                ],
            }
        },
        methods: {
            onSubmit () {
                this.error = false;
                this.message = '';
                this.$store.dispatch('resetSend', this.user)
                    .then(() => this.message = 'Письмо отправлено вам на почту')
                    .catch(err => {
                        this.error = 'E-mail не корректен!'
                    })
            }
        },
        computed: {
        }
    }
</script>
