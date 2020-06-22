<template>
  <v-container fluid class="mt-5 mb-5">
    <v-layout align-center justify-center>
      <v-flex :xs10="!userId" :xs12="userId">
        <v-card class="elevation-12 ">
          <v-toolbar dark class="gradient-project">
            <v-toolbar-title class="w-100 text-center">Пользователь: {{ user.name + ' ' + user.last_name }}
            </v-toolbar-title>
          </v-toolbar>

          <div class="file-input-area mx-auto" v-if="userId">
            <ImagePicker
                v-model="images"
                :max="1"
                :activeImageUploads="activeImageUploads"
                exceedMaxImagesError="Вы выбрали слишком много картинок"
                invalidFileTypeError="Загрузите картинку типа: png, jpg или gif"
                maxImagesUnit="Изображений"
                clearedImagesMessage="Очищено"
                clearImagesLabel="Очистить"
                addImagesLabel="Добавить"
                justify-content
            >
              <v-flex xs4 md3>
                <img :src="image" onerror="if (this.src != '/images/empty-user.jpg') this.src = '/images/empty-user.jpg';" width="100%" height="100%">
              </v-flex>
            </ImagePicker>
          </div>


          <v-img
              class="white--text align-end mx-auto  mt-2" height="200px" width="200px"
              onerror="if (this.src != '/images/empty-user.jpg') this.src = '/images/empty-user.jpg';"
              :src="image" style="overflow: inherit"
              v-if="!userId"
          >
            <v-btn
                :color="user.email_verified_at ? '#FF9765' : 'gray'" dark small absolute bottom left fab
                :title="user.email_verified_at ? 'Верифицирован' : 'НЕ Верифицирован'">
              <v-icon>mdi-shield-check</v-icon>
            </v-btn>
          </v-img>


          <v-card-text class="text--primary mt-1 mx-auto" style="max-width: 400px">

            <v-form v-model="valid" ref="form" validation>
              <v-list two-line subheader>
                <v-subheader>Информация:</v-subheader>

                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-title>Имя</v-list-item-title>
                    <v-list-item-subtitle v-if="!userId">{{ user.name }}</v-list-item-subtitle>
                    <v-text-field
                        v-if="userId"
                        v-model="user.name"
                        label="Имя"
                        :rules="nameRules"
                        color="#ff9966"
                    />
                  </v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-title>Фамилия</v-list-item-title>
                    <v-list-item-subtitle v-if="!userId">{{ user.last_name }}</v-list-item-subtitle>
                    <v-text-field
                        v-if="userId"
                        v-model="user.last_name"
                        label="Фамилий"
                        :rules="lastNameRules"
                        color="#ff9966"
                    />
                  </v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-title>Пол</v-list-item-title>
                    <v-list-item-subtitle v-if="!userId">{{ user.sex }}</v-list-item-subtitle>
                    <v-select
                        v-if="userId"
                        name="sex"
                        :items="sexItems"
                        v-model="user.sex"
                        item-color="#ff9966"
                        aria-required="true"
                        label="Пол"
                        color="#ff9966"
                        :rules="selectedRules"
                    />
                  </v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-title>Дата Рождения</v-list-item-title>
                    <v-list-item-subtitle v-if="!userId">{{ user.date_of_birth }}</v-list-item-subtitle>
                    <v-menu
                        ref="menu"
                        v-model="menu"
                        v-if="userId"
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
                  </v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-title>Дата регистрации</v-list-item-title>
                    <v-list-item-subtitle>{{ user.created_at.substr(0, 10) }}</v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-title>Статус</v-list-item-title>
                    <v-list-item-subtitle>{{ user.role }}</v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
                <v-list-item v-if="userId">
                  <v-list-item-content>
                    <v-list-item-title>Телефон</v-list-item-title>
                    <v-list-item-subtitle>{{ user.phone }}</v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
                <v-list-item v-if="userId">
                  <v-list-item-content>
                    <v-list-item-title>Почта</v-list-item-title>
                    <v-list-item-subtitle>{{ user.email }}</v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
                <v-list-item v-if="userId">
                  <v-list-item-content>
                    <v-btn
                        color="#FF5F66"
                        dark
                        class="gradient-project"
                        @click="saveItem()"
                        :disabled="!valid"
                        v-bind:dark="valid"
                    >
                      Сохранить
                    </v-btn>
                  </v-list-item-content>
                </v-list-item>
                <v-list-item v-if="userId">
                  <v-list-item-content>
                    <v-btn
                        color="#FF5F66"
                        dark
                        class="gradient-project"
                        :to="'/reset-send'"
                    >
                      Сброс пароля
                    </v-btn>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-form>
          </v-card-text>
        </v-card>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  import { ImagePicker, imageUploadingStates } from '@nagoos/vue-image-picker'

  export default {
    components: {
      ImagePicker
    },
    props: ['userId'],
    data () {
      return {
        id: null,
        menu: false,
        valid: false,
        images: [],
        activeImageUploads: {},
        image: '/images/empty-user.jpg',
        user: { created_at: '' },
        sexItems: ['Мужчина', 'Женщина'],
        nameRules: [
          // v => !!v || 'Заполните имя!',
          v => (v && v.length >= 3) || 'Имя должно быть длинной более 3-х символов'
        ],
        lastNameRules: [
          v => !!v || 'Заполните фамилию!',
          v => (v && v.length >= 3) || 'Фамилия должна быть длинной более 3-х символов'
        ],
        dateRules: [
          v => !!v || 'Введите дату!'
        ],
        selectedRules: [
          v => !!v || 'Выберите элементы!',
        ],
      }
    },
    created () {
      this.initialize();
    },
    methods: {
      initialize () {
        let id = Number.parseInt(this.$route.params.id) || this.userId

        this.$store.dispatch('getOneEntity', { entity: 'users', id })
          .then((resp) => {
            this.user = resp.data
            this.image = `/storage/users/${this.user.slug}.png?d=${new Date()}`
          })
          .catch(err => this.$router.push('/'))
      },
      saveItem () {
        let user = {
          name: this.user.name,
          last_name: this.user.last_name,
          role: this.user.role,
          sex: this.user.sex,
          date_of_birth: this.user.date_of_birth,
          email: this.user.email,
          phone: this.user.phone
        }
        this.$store.dispatch('putEntity', { data: user, id: this.user.id, entity: 'users/update-user' })
          .then((resp) => {
            this.createPhoto(resp.data)
            this.initialize()
          })
          .catch(err => {
              this.initialize()
              this.error = 'Ошибка обновления'
            }
          )
      },
      createPhoto () {
        if (this.images.length > 0) {
          this.$store.dispatch('uploadPhotoUser', {id: this.userId, data: { type: 'users', file: this.images[0], slug: this.user.slug }})
            .then((resp) => {
              console.log(resp)
            }).catch(err => {console.log(err)}).finally(()=> {
            document.querySelectorAll('.v-btn--depressed')[0].click();
          })
        }
      }
    }
  }
</script>

<style>
  .file-input {
    display: none !important;
  }

  .file-input-area i {
    display: none !important;
  }

  .file-input-area .layout.justify-start.wrap {
    width: 30%;
    margin: auto!important;
  }

  @media screen  and (max-device-width: 480px) {

    .file-input-area .layout.justify-start.wrap {
      width: 90%;
      margin: auto !important;
    }
  }
  .file-input-area .layout {
    display: flex;
    justify-content: center;
  }

  .file-input-area .layout div {
    text-align: center;
  }

  .v-snack__content {
    background: linear-gradient(180deg, #FF5F66, #FF9765);
  }

  .v-snack {
    opacity: 0;
  }

  .text-up-line {
    text-shadow: 2px 0 2px #000000,
    0 2px 2px #000000,
    -2px 0 2px #000000,
    0 -2px 2px #000000;
  }
</style>