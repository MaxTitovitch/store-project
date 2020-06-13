<template>
  <div>
    <v-data-table
        v-model="selected"
        :headers="headers"
        :items="items"
        item-key="id"
        show-select
        class="elevation-1 mt-5"
        :search="search"
        :loading="isLoading"
        color="#FF9765"
        loading-text="Загрузка... Пожалуйста, подаждите"
    >
      <template v-slot:item.email_verified_at="{ item }">
        <v-chip>{{ item.email_verified_at ? 'Да' : 'Нет' }}</v-chip>
      </template>
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">CRUD опеарции: Пользователи</v-toolbar-title>
          <v-divider
              class="mx-4"
              inset
              vertical
          />
          <v-spacer/>
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                  color="#FF5F66"
                  dark
                  class="gradient-project mx-2"
                  :disabled="selected.length < 1"
                  @click="deleteItems()"
              >Удалить
              </v-btn>
              <v-btn
                  color="#FF5F66"
                  dark
                  class="gradient-project mx-2"
                  v-bind="attrs"
                  v-on="on"
                  @click="createItem()"
              >Добавить
              </v-btn>
              <v-text-field
                  v-model="search"
                  append-icon="mdi-magnify"
                  label="ПОИСК..."

                  hide-details
                  dark
                  style="max-width: 20%"
                  class="mx-2"
              />
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-form v-model="valid" ref="form" validation>
                    <v-row>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field color="#FF9765" v-model="editedItem.name" :rules="nameRules" label="Имя"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field color="#FF9765" v-model="editedItem.last_name" :rules="lastNameRules"
                                      label="Фамилия"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-select
                            name="sex"
                            :items="sexItems"
                            v-model="editedItem.sex"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Пол"
                            color="#ff9966"
                        />
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
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
                                v-model="editedItem.date_of_birth"
                                label="Дата Рождения"
                                readonly
                                v-on="on"
                                :rules="dateRules"
                                color="#ff9966"
                            />
                          </template>
                          <v-date-picker
                              v-model="editedItem.date_of_birth"
                              scrollable
                              locale="ru-ru"
                              :max="new Date().toISOString().substr(0, 10)"
                              min="1900-01-01"
                              first-day-of-week="1"
                              header-color="#ff9765"
                          >
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="menu = false">Отмена</v-btn>
                            <v-btn text color="primary" @click="$refs.menu.save(editedItem.date_of_birth)">ОК</v-btn>
                          </v-date-picker>
                        </v-menu>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field color="#FF9765" :disabled="editedIndex > -1" v-model="editedItem.email" :rules="emailRules" label="Почта"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field color="#FF9765" :disabled="editedIndex > -1" v-model="editedItem.phone" :rules="phoneRules" label="Телефон"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field color="#FF9765" v-model="password" type="password"
                                      :rules="passwordRules" @input="onChangePassword" label="Пароль"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field color="#FF9765" v-model="confirm_password" type="password"
                                      :rules="confirmPasswordRules" label="Подтвердите пароль"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-select
                            name="sex"
                            v-model="editedItem.role"
                            :items="roleItems"
                            aria-required="true"
                            item-color="#ff9966"
                            label="Роль"
                            color="#ff9966"
                        />
                      </v-col>
                    </v-row>
                  </v-form>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer/>
                <v-btn dark class="gradient-project" text @click="close">Отмена</v-btn>
                <v-btn dark class="gradient-project" text
                       @click="save"
                       :disabled="!valid"
                       v-bind:dark="valid"
                >Сохранить
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon
            small
            class="mr-2"
            @click="showItem(item.id)"
        >
          mdi-eye
        </v-icon>
        <v-icon
            small
            class="mr-2"
            @click="editItem(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
            small
            @click="deleteItem(item)"
        >
          mdi-delete
        </v-icon>
        <v-icon
            small
            @click="showChart(item.id)"
        >
          mdi-chart-bell-curve-cumulative
        </v-icon>
        <v-icon
            small
            @click="sendVerify(item)"
            v-if="!item.email_verified_at"
        >
          mdi-shield-check
        </v-icon>
      </template>
      <template v-slot:no-data>
        <v-btn dark class="gradient-project" @click="initialize">Повторить загрузку</v-btn>
      </template>
    </v-data-table>
    <v-alert type="success" v-if="message">
      {{ message }}
    </v-alert>
    <v-alert type="error" v-if="error">
      {{ error }}
    </v-alert>
  </div>

</template>

<script>
  export default {
    data () {
      return {
        search: '',
        menu: '',
        message: '',
        error: '',
        dialog: false,
        valid: false,
        selected: [],
        roleItems: ['Пользователь', 'Администратор', 'Главный администратор'],
        sexItems: ['Мужчина', 'Женщина'],
        headers: [
          { text: 'ИД', value: 'id' },
          { text: 'Имя', value: 'name' },
          { text: 'Фамилия', value: 'last_name' },
          { text: 'Пол', value: 'sex' },
          { text: 'Дата рождения', value: 'date_of_birth' },
          { text: 'Почта', value: 'email' },
          { text: 'Верифицирован?', value: 'email_verified_at', sortable: false},
          { text: 'Телефон', value: 'phone' },
          { text: 'Роль', value: 'role' },
          { text: 'Действия', value: 'actions', sortable: false },
        ],
        items: [],
        desserts: [],
        editedIndex: -1,
        password: '',
        confirm_password: '',
        editedItem: {
          name: '',
          last_name: '',
          sex: 'Мужчина',
          date_of_birth: '',
          email: '',
          email_verified_at: '',
          phone: '',
          role: 'Пользователь',
        },
        defaultItem: {
          name: '',
          last_name: '',
          sex: 'Мужчина',
          date_of_birth: '',
          email: '',
          email_verified_at: '',
          phone: '',
          role: 'Пользователь',
        },
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
        passwordRules: [],
        passwordRulesCreate: [
          v => !!v || 'Введите пароль!',
          v => {
            return (v && v.length >= 8) || 'Пароль должен быть длинной более 8-ми символов'
          }
        ],
        passwordRulesEdit: [
          v => {
            if (!v) {
              return true;
            }
            return (v.length >= 8) || 'Пароль должен быть длинной более 8-ми символов'
          }
        ],
        confirmPasswordRules: [
          v => (v === this.password) || 'Пароли должены совпадать'
        ],
        sexRules: [
          v => !!v || 'Выберите пол!',
        ],
        phoneRules: [
          v => !!v || 'Введите номер телефона!',
          v => (v && !isNaN(v) && (v + '').length === 9) || 'Введите номер в формате: YYXXXXXXX!',
        ],
        dateRules: [
          v => !!v || 'Введите дату!'
        ]
      }
    },
    created () {
      this.initialize()

    },
    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Создание пользователя' : 'Редактирование пользователя'
      },
      isLoading: function () {return this.$store.getters.isLoading }
    },
    watch: {
      dialog (val) {
        val || this.close()
      },
    },
    methods: {
      onChangePassword () {
        this.confirm_password = null
      },
      initialize () {
        this.$store.dispatch('getUsers', { order: 'id asc' })
          .then((resp) => {
            this.items = resp.data
          })
          .catch(err => this.$router.push('/'))
      },

      showItem (id) {
        this.$router.push('/users/' + id)
      },

      showChart (id) {
        this.$router.push(`/line-chart?entity=product&id=${id}&param=views`)
      },

      sendVerify (id) {
        this.$store.dispatch('verifySend')
          .then(() => {
            this.message = 'Письмо отправлено на почту'
          })
          .catch(err => {
            this.error = 'Ошибка верификации'
          })
      },

      editItem (item) {
        this.password = '';
        this.confirm_password = '';
        this.passwordRules = this.passwordRulesEdit;
        this.editedIndex = this.items.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      createItem () {
        this.password = '';
        this.confirm_password = '';
        this.passwordRules = this.passwordRulesCreate;
        this.editedIndex = -1;
        this.dialog = true
      },

      deleteItem (item) {
        this.$store.dispatch('deleteUsers', {id: item.id})
          .then((resp) => {
            const index = this.items.indexOf(item)
            this.items.splice(index, 1)
          })
          .catch(err => {
              this.initialize()
              this.error = 'Ошибка удаления'
            }
          )
      },

      deleteItems () {
        for (let i = 0; i < this.selected.length; i++) {
          this.deleteItem(this.selected[i])
        }
      },

      close () {
        this.dialog = false
        this.$nextTick(() => {
          this.password = '';
          this.confirm_password = '';
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      save () {
        let user = {
          name: this.editedItem.name,
          last_name: this.editedItem.last_name,
          role: this.editedItem.role,
          sex: this.editedItem.sex,
          date_of_birth: this.editedItem.date_of_birth,
          email: this.editedItem.email,
          phone: this.editedItem.phone
        };
        if(this.password != '') {
          user.password = this.password;
          user.confirm_password = this.confirm_password;
        }
        if (this.editedIndex > -1) {
          this.$store.dispatch('putUsers', {data: user, id: this.editedItem.id})
          .then((resp) => {
            this.initialize()
          })
          .catch(err => {
              this.initialize()
              this.error = 'Ошибка обновления'
            }
          )
        } else {
          console.log(user)
          this.$store.dispatch('postUsers', user)
            .then((resp) => {
              this.initialize()
            })
            .catch(err => {
              this.initialize()
              this.error = 'Ошибка добавления'
              }
            )
        }
        this.close()
      },
    }
  }
</script>

<style scoped>


</style>