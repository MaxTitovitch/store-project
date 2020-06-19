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
      <template v-slot:item.entity_type="{ item }">
        {{ getTextByValue(item.entity_type, typeItems) }}
      </template>
      <template v-slot:item.user_id="{ item }">
        {{ getTextByValue(item.user_id, userItems) }}
      </template>
      <template v-slot:item.entity_id="{ item }">
        {{ getTextByValue(item.entity_id, item.entity_type === 'product' ? productItems : postItems) }}
      </template>
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">CRUD опеарции: Комментарии</v-toolbar-title>
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
                      <v-col cols="12" sm="12" md="12">
                        <v-textarea
                            color="#FF9765"
                            v-model="editedItem.text"
                            :rules="textRules"
                            label="Текст"
                            filled
                            auto-grow
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
                                v-model="editedItem.date"
                                label="Дата написания"
                                readonly
                                v-on="on"
                                :rules="dateRules"
                                color="#ff9966"
                            />
                          </template>
                          <v-date-picker
                              v-model="editedItem.date"
                              scrollable
                              locale="ru-ru"
                              :max="new Date().toISOString().substr(0, 10)"
                              min="2015-01-01"
                              first-day-of-week="1"
                              header-color="#ff9765"
                          >
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="menu = false">Отмена</v-btn>
                            <v-btn text color="primary" @click="$refs.menu.save(editedItem.date)">ОК</v-btn>
                          </v-date-picker>
                        </v-menu>
                      </v-col>

                      <v-col cols="12" sm="6" md="4">
                        <v-select
                            :items="typeItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.entity_type"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Тип"
                            color="#ff9966"
                            :rules="selectedRules"
                        />
                      </v-col>

                      <v-col cols="12" sm="6" md="4">
                        <v-select
                            name="sex"
                            :items="entityItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.entity_id"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Сущность"
                            color="#ff9966"
                            :rules="selectedRules"
                        />
                      </v-col>

                      <v-col cols="12" sm="6" md="4">
                        <v-select
                            name="sex"
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
        addImagesLabel: 'Добавить',
        search: '',
        menu: '',
        message: '',
        error: '',
        dialog: false,
        valid: false,
        selected: [],
        typeItems: [{ value: 'post', text: 'Статьи' }, { value: 'product', text: 'Товары' },],
        entityItems: [],
        postItems: [],
        productItems: [],
        userItems: [],
        editedEntity: {},
        headers: [
          { text: 'ИД', value: 'id' },
          { text: 'Текст', value: 'text' },
          { text: 'Дата написания', value: 'date' },
          { text: 'Тип', value: 'entity_type' },
          { text: 'Сущность', value: 'entity_id' },
          { text: 'Пользователь', value: 'user_id' },
          { text: 'Действия', value: 'actions', sortable: false },
        ],
        items: [],
        editedIndex: -1,
        editedItem: {
          text: '',
          date: '',
          entity_type: '',
          entity_id: '',
          user_id: '',
        },
        defaultItem: {
          text: '',
          date: '',
          entity_type: '',
          entity_id: '',
          user_id: '',
        },
        textRules: [
          // v => !!v || 'Заполните имя!',
          v => (v && v.length >= 10) || 'Текст должен быть длинной более 10-х символов'
        ],
        dateRules: [
          v => !!v || 'Введите дату!'
        ],
        selectedRules: [
          v => !!v || 'Выберите элемент!',
        ],
      }
    },

    created () {
      this.initialize()
    },
    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Создание комментария' : 'Редактирование комментария'
      },
      isLoading: function () { return this.$store.getters.isLoading }
    },
    watch: {
      dialog (val) {
        val || this.close()
      },
      'editedItem.entity_type': function (val) {
        if (val === 'product') {
          this.entityItems = this.productItems
        } else if (val === 'post') {
          this.entityItems = this.postItems
        }
        this.editedItem.entity_id = this.editedEntity.type === val ? this.editedEntity.id : ''
      }
    },
    methods: {
      initialize () {
        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'comments' })
          .then((resp) => {
            this.items = resp.data
          })
          .catch(err => this.$router.push('/'))
        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'products' })
          .then((resp) => {
            for (let i = 0; i < resp.data.length; i++) {
              this.productItems.push({ text: resp.data[i].name, value: resp.data[i].id })
            }
          })
          .catch(err => this.$router.push('/'))
        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'posts' })
          .then((resp) => {
            for (let i = 0; i < resp.data.length; i++) {
              this.postItems.push({ text: resp.data[i].name, value: resp.data[i].id })
            }
          })
          .catch(err => this.$router.push('/'))
        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'users' })
          .then((resp) => {
            for (let i = 0; i < resp.data.length; i++) {
              this.userItems.push({ text: `${resp.data[i].name} ${resp.data[i].last_name}`, value: resp.data[i].id })
            }
          })
          .catch(err => this.$router.push('/'))
      },

      editItem (item) {
        this.editedEntity = { type: item.entity_type, id: item.entity_id }
        this.editedIndex = this.items.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      createItem () {
        this.editedIndex = -1
        this.dialog = true
      },

      deleteItem (item) {
        this.$store.dispatch('deleteEntity', { entity: 'comments', id: item.id })
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
        let comment = {
          text: this.editedItem.text,
          date: this.editedItem.date,
          entity_type: this.editedItem.entity_type,
          entity_id: this.editedItem.entity_id,
          user_id: this.editedItem.user_id,
        }
        if (this.editedIndex > -1) {
          this.$store.dispatch('putEntity', { data: comment, id: this.editedItem.id, entity: 'comments' })
            .then((resp) => {
              this.initialize()
            })
            .catch(err => {
                this.initialize()
                this.error = 'Ошибка обновления'
              }
            )
        } else {
          this.$store.dispatch('postEntity', { entity: 'comments', data: comment })
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
      }
    }
  }
</script>

<style>

</style>