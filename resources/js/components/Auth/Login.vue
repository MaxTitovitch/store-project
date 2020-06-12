<template>
  <v-container fluid fill-height>
    <v-layout align-center justify-center>
      <v-flex xs12 sm8 md6>
        <v-card class="elevation-12 ">
          <v-toolbar dark class="gradient-project">
            <v-toolbar-title>Вход в систему</v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-alert type="error" v-if="error">
              {{ error }}
            </v-alert>
            <v-form v-model="valid" ref="form" validation>
              <v-text-field
                  prepend-icon="mdi-account"
                  name="email"
                  label="E-mail или Номер телефона"
                  type="text"
                  v-model="user.login"
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
            </v-form>
          </v-card-text>
            <v-card-actions>
                <v-spacer/>
                <v-btn href="/reset-send" text>Забыли пароль? Востонавить?</v-btn>
            </v-card-actions>
            <v-card-actions>
            <v-spacer/>
            <v-btn
                class="gradient-project"
                :disabled="!valid"
                v-bind:dark="valid"
                large
                @click="onSubmit"
            >Вход
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
          login: '',
          password: '',
        },
        valid: false,
        error: false,
        emailRules: [
          v => !!v || 'Введите E-mail или Номер телефона!',
          v => (v && v.length >= 6) || 'Логин должен быть длинной более 6-ти символов'
        ],
        passwordRules: [
          v => !!v || 'Заполните пароль!',
          v => (v && v.length >= 6) || 'Пароль должен быть длинной более 6-ти символов'
        ]
      }
    },
    methods: {
      onSubmit () {
        this.error = false
        this.$store.dispatch('login', this.user)
          .then(() => this.$router.push('/'))
          .catch(err => {
            this.error = 'Логин и\\или пароль некорректны!'
          })
      }
    },
    computed: {
      // mainColor () {
      //     return this.$store.getters.mainColor
      // }
    }
  }
</script>
