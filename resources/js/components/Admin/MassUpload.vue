<template>
  <v-container fluid fill-height>
    <v-layout align-center justify-center>
      <v-flex xs12 sm8 md6>
        <v-card class="elevation-12 ">
          <v-toolbar dark class="gradient-project">
            <v-toolbar-title>Массовая загрузка товаров</v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-alert type="success" v-if="message">
              {{ message }}
            </v-alert>
            <v-alert type="error" v-if="error">
              {{ error }}
            </v-alert>
            <v-form v-model="valid" ref="form" validation>
              <v-file-input accept=".xml,.json" label="Файл данных" color="#ff9966" :rules="fileRules"  ref="file" v-model="file"/>
              <v-switch
                  v-model="type"
                  :label="`Жёсткая загрузка: ${type ? 'Да' : 'Нет'}`"
                  color="#ff9966"
              />
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
            >Загрузить
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-flex>
    </v-layout>
    <div v-if="isLoggedRole !== 'Главный администратор'" class="lock"></div>
  </v-container>
</template>

<script>
  export default {
    data () {
      return {
        type: '',
        file: null,
        valid: false,
        error: false,
        message: false,
        fileRules: [
          v => !!v || 'Загрузите файл!',
        ],
      }
    },
    computed: {
      isLoggedRole: function () { return this.$store.getters.isLoggedRole },
    },
    methods: {
      onSubmit () {
        this.error = false
        this.$store.dispatch('uploadFile', {type: this.type === true ? 'hard' : 'soft', file: this.file})
          .then((resp) => {
            this.message = 'Данные загружены!'
          })
          .catch(err => {
            this.error = 'Данные не корректны!'
          })
      },
    }
  }
</script>

<style>

  .lock {
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    background-image: url('/lock.jpg');
    background-position: center;
    background-size: 100% 100%;
  }
</style>
