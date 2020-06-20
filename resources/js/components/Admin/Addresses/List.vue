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
        :footerProps="{
          itemsPerPageText: 'Элементов на странице',
          'items-per-page-options': [5, 10, 20, {text: 'Всё', value: -1}],
        }"
    >
      <template v-slot:item.user_id="{ item }">
        {{ getTextByValue(item.user_id, userItems) || '-' }}
      </template>
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">CRUD опеарции: Адреса</v-toolbar-title>
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
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.country"
                            :rules="textRules"
                            label="Страна"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.city"
                            :rules="textRules"
                            label="Город"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.street"
                            :rules="textRules"
                            label="Улица"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.house"
                            :rules="textRules"
                            label="Дом"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.perch"
                            :rules="numberRules"
                            label="Подъезд"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.floor"
                            :rules="numberRules"
                            label="Этаж"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.apartment"
                            :rules="numberRules"
                            label="Квартира"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="8">
                        <v-select
                            :items="userItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.user_id"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Пользователь"
                            color="#ff9966"
                            :rules="selectedRules"
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
        userItems: [],
        headers: [
          { text: 'ИД', value: 'id' },
          { text: 'Страна', value: 'country' },
          { text: 'Город', value: 'city' },
          { text: 'Улица', value: 'street' },
          { text: 'Дом', value: 'house' },
          { text: 'Подъезд', value: 'perch' },
          { text: 'Этаж', value: 'floor' },
          { text: 'Квартира', value: 'apartment' },
          { text: 'Пользователь', value: 'user_id' },
          { text: 'Действия', value: 'actions', sortable: false },
        ],
        items: [],
        editedIndex: -1,
        editedItem: {
          country: '',
          city: '',
          street: '',
          house: '',
          perch: '',
          floor: '',
          apartment: '',
          user_id: '',
        },
        defaultItem: {
          country: '',
          city: '',
          street: '',
          house: '',
          perch: '',
          floor: '',
          apartment: '',
          user_id: '',
        },
        textRules: [
          v => !!v || 'Введите значение!',
          v => (v && v.length <= 25) || 'Текст должен быть длинной менее 25-х символов'
        ],
        numberRules: [
          v => !!v || 'Введите значение!',
          v => (v && !isNaN(v) && v > 0 && v < 1000) || 'Введите число от 0 до 1000!',
        ],
        selectedRules: [
          v => !!v || 'Выберите элементы!',
        ],
      }
    },

    created () {
      this.initialize()
    },
    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Создание адреса' : 'Редактирование адреса'
      },
      isLoading: function () { return this.$store.getters.isLoading }
    },
    watch: {
      dialog (val) {
        val || this.close()
      },
    },
    methods: {
      initialize () {
        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'addresses' })
          .then((resp) => {
            this.items = resp.data
          })
          .catch(err => this.$router.push('/'))

        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'users' })
          .then((resp) => {
            for (let i = 0; i < resp.data.length; i++) {
              this.userItems.push({ text: `${resp.data[i].name} ${resp.data[i].last_name}`, value: resp.data[i].id })
            }
          })
          .catch(err => { this.$router.push('/')})

      },

      editItem (item) {
        this.editedIndex = this.items.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      createItem () {
        this.editedIndex = -1
        this.dialog = true
      },

      deleteItem (item) {
        this.$store.dispatch('deleteEntity', { entity: 'addresses', id: item.id })
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
        this.editedEntity = {}
        this.$refs.form.reset()
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      save () {
        let address = {
          country: this.editedItem.country,
          city: this.editedItem.city,
          street: this.editedItem.street,
          house: this.editedItem.house,
          perch: this.editedItem.perch,
          floor: this.editedItem.floor,
          apartment: this.editedItem.apartment,
          user_id: this.editedItem.user_id
        }
        if (this.editedIndex > -1) {
          this.$store.dispatch('putEntity', { data: address, id: this.editedItem.id, entity: 'addresses' })
            .then((resp) => {
              this.initialize()
            })
            .catch(err => {
                this.initialize()
                this.error = 'Ошибка обновления'
              }
            )
        } else {
          this.$store.dispatch('postEntity', { entity: 'addresses', data: address })
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
      getTextByValue (value, items) {
        for (let i = 0; i < items.length; i++) {
          if (items[i].value === value) {
            return items[i].text
          }
        }
        return value
      },
    }
  }
</script>

<style>

</style>