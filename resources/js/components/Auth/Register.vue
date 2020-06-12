<template>
    <v-container fluid fill-height>
        <v-layout align-center justify-center>
            <v-flex xs12 sm8 md6>
                <v-card class="elevation-12 ">
                    <v-toolbar dark class="gradient-project">
                        <v-toolbar-title>Регистрация</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form v-model="valid" ref="form" validation>
                            <v-text-field
                                name="name"
                                label="Имя"
                                type="text"
                                v-model="user.name"
                                aria-required="true"
                                :rules="nameRules"
                                color="#ff9966"
                            />
                            <v-text-field
                                name="last_name"
                                label="Фамилия"
                                type="text"
                                v-model="user.last_name"
                                aria-required="true"
                                :rules="lastNameRules"
                                color="#ff9966"
                            />
                            <v-select
                                name="sex"
                                :items="sexItems"
                                v-model="user.sex"
                                :rules="sexRules"
                                label="Пол"
                                color="#ff9966"
                            />
                            <v-text-field
                                name="phone"
                                label="Телефон +375 YYXXXXXXX"
                                type="text"
                                v-model="user.phone"
                                aria-required="true"
                                :rules="phoneRules"
                                color="#ff9966"
                            />

                            <v-menu
                                ref="menu"
                                v-model="menu"
                                :close-on-content-click="false"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                                color="#ff9966"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="user.date_of_birth"
                                        label="Дата Рождения"
                                        readonly
                                        v-on="on"
                                        :rules="dateRules"
                                        color="#ff9966"
                                    />
                                </template>
                                <v-date-picker
                                    v-model="user.date_of_birth"
                                    scrollable
                                    locale="ru-ru"
                                    :max="new Date().toISOString().substr(0, 10)"
                                    min="1900-01-01"
                                    first-day-of-week="1"
                                    header-color="#ff9765"
                                >
                                    <v-spacer></v-spacer>
                                    <v-btn text color="primary" @click="menu = false">Отмена</v-btn>
                                    <v-btn text color="primary" @click="$refs.menu.save(user.date_of_birth)">ОК</v-btn>
                                </v-date-picker>
                            </v-menu>
                            <v-text-field
                                prepend-icon="mdi-account"
                                name="email"
                                label="E-mail"
                                type="email"
                                v-model="user.email"
                                aria-required="true"
                                :rules="emailRules"
                                color="#ff9966"
                            />
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
                                v-model="user.confirm_password"
                                aria-required="true"
                                :rules="confirmPasswordRules"
                                color="#ff9966"

                            />
                            <div v-if="error" class="red--text">
                                {{ error }}
                            </div>
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
                        >Регистрация
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
                    email: '',
                    name: '',
                    last_name: '',
                    password: '',
                    confirm_password: '',
                    sex: '',
                    phone: '',
                    date_of_birth: ''
                },
                menu: false,
                error: false,
                valid: false,
                sexItems: ["Мужчина", "Женщина"],
                emailRules: [
                    v => !!v || 'Заполните E-mail!',
                    v => /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail должен быть валиден'
                ],
                nameRules: [
                    v => !!v || 'Заполните имя!',
                    v => (v && v.length >= 3) || 'Имя должно быть длинной более 3-х символов'
                ],
                lastNameRules: [
                    v => !!v || 'Заполните фамилию!',
                    v => (v && v.length >= 3) || 'Фамилия должна быть длинной более 3-х символов'
                ],
                passwordRules: [
                    v => !!v || 'Заполните пароль!',
                    v => (v && v.length >= 8) || 'Пароль должен быть длинной более 8-ми символов'
                ],
                confirmPasswordRules: [
                    v => !!v || 'Подтвердите пароль!',
                    v => (v && v === this.user.password) || 'Пароли должены совпадать'
                ],
                sexRules: [
                    v => !!v || 'Выберите пол!',
                ],
                phoneRules: [
                    v => !!v || 'Введите номер телефона!',
                    v => (v && v.length === 9 && !isNaN(v))|| 'Введите номер в формате: YYXXXXXXX!'
                ],
                dateRules: [
                    v => !!v || 'Введите дату!'
                ]
            }
        },
        methods: {
            onSubmit () {
                this.error = false;
                this.$store.dispatch('register', this.user)
                    .then(() => this.$router.push('/'))
                    .catch(err => {
                        console.log(err)
                        this.error = 'Данные некорректны!'
                    })
            }
        },
        computed: {
        }
    }
</script>
