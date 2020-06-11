<template>
  <v-container fluid fill-height>
    <v-layout align-center justify-center>
      <v-flex xs12 sm8 md6>
        <v-card class="elevation-12 ">
          <v-toolbar dark class="gradient-project">
            <v-toolbar-title>Вы не подтвердили свой email. Отправить письмо ещё раз?</v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-alert type="success" v-if="message">
              {{ message }}
            </v-alert>
            <v-alert type="error" v-if="error">
              {{ error }}
            </v-alert>
          </v-card-text>
          <v-card-actions>
            <v-layout align-center justify-center>
              <v-flex xs12 sm8 md6>
                <v-btn
                    class="gradient-project"
                    large
                    @click="onSubmit"
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
        message: '',
        error: '',
      }
    },
    methods: {
      onSubmit () {
        this.$store.dispatch('verifySend')
          .then(() => {
            this.message = 'Письмо отправлено вам на почту'
          })
          .catch(err => {
            this.error = 'Ошибка верификации'
          })
      }
    }
  }
</script>
