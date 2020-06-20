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
      <template v-slot:item.parent_id="{ item }">
        {{ getTextByValue(item.parent_id, categoryItems) || '-' }}
      </template>
      <template v-slot:item.characteristics="{ item }">
        {{ getCharacteristics(item.characteristics) || '-' }}
      </template>
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">CRUD опеарции: Категории</v-toolbar-title>
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
                      <v-col cols="12" sm="6" md="6">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.name"
                            :rules="textRules"
                            label="Название"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="6">
                        <v-select
                            :items="categoryItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.parent_id"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Родительская категория"
                            color="#ff9966"
                            clearable
                        />
                      </v-col>
                      <v-col cols="12" sm="12" md="12">
                        <v-select
                            :items="characteristicItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.characteristics"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Характеристики"
                            color="#ff9966"
                            multiple
                            chips
                            clearable
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
        categoryItems: [],
        characteristicItems: [],
        headers: [
          { text: 'ИД', value: 'id' },
          { text: 'Название', value: 'name' },
          { text: 'Родительская категория', value: 'parent_id' },
          { text: 'Характеристики', value: 'characteristics' },
          { text: 'Действия', value: 'actions', sortable: false },
        ],
        items: [],
        editedIndex: -1,
        editedItem: {
          name: '',
          parent_id: '',
          characteristics: '',
        },
        defaultItem: {
          name: '',
          parent_id: '',
          characteristics: '',
        },
        textRules: [
          v => !!v || 'Введите значение!',
          v => (v && v.length >= 3) || 'Текст должен быть длинной более 3-х символов',
          v => (v && v.length <= 100) || 'Текст должен быть длинной менее 100-х символов'
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
        return this.editedIndex === -1 ? 'Создание категории' : 'Редактирование категории'
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
        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'categories' })
          .then((resp) => {
            this.items = resp.data
            for (let i = 0; i < this.items.length; i++) {
              for (let j = 0; j < this.items[i].characteristics.length; j++) {
                this.items[i].characteristics[j] = {
                  text: this.items[i].characteristics[j].name,
                  value: this.items[i].characteristics[j].id
                }
              }
            }
          })
          .catch(err => this.$router.push('/'))

        this.$store.dispatch('getEntity', { data: { where: 'parent_id IS NULL', order: 'id asc' }, entity: 'categories' })
          .then((resp) => {
            for (let i = 0; i < resp.data.length; i++) {
              this.categoryItems.push({ text: resp.data[i].name, value: resp.data[i].id })
            }
          })
          .catch(err => { this.$router.push('/')})

        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'characteristics' })
          .then((resp) => {
            for (let i = 0; i < resp.data.length; i++) {
              this.characteristicItems.push({ text: resp.data[i].name, value: resp.data[i].id })
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
        this.$store.dispatch('deleteEntity', { entity: 'categories', id: item.id })
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
        let category = {
          name: this.editedItem.name,
          parent_id: this.editedItem.parent_id || null,
        }, ids = [];
        console.log(category)
        for (let i = 0; i < this.editedItem.characteristics.length; i++) {
          ids.push(this.editedItem.characteristics[i].value || this.editedItem.characteristics[i])
        }
        if (this.editedItem.id === this.editedItem.parent_id) category.parent_id = null
        if (this.editedIndex > -1) {
          this.$store.dispatch('putEntity', { data: category, id: this.editedItem.id, entity: 'categories' })
            .then((resp) => {
              // if(ids.length > 0)
                this.$store.dispatch('putEntity', { entity: 'category-characteristic', data: { 'characteristics': ids }, id: resp.data.id })
                  .then((resp) => {this.initialize()})
                  .catch(err => {
                    this.initialize()
                    this.error = 'Ошибка добавления'
                  })
              this.initialize()
            })
            .catch(err => {
                this.initialize()
                this.error = 'Ошибка обновления'
              }
            )
        } else {
          this.$store.dispatch('postEntity', { entity: 'categories', data: category })
            .then((resp) => {
              // if(ids.length > 0)
                this.$store.dispatch('putEntity', { entity: 'category-characteristic', data: { 'characteristics': ids }, id: resp.data.id })
                  .then((resp) => {this.initialize()})
                  .catch(err => {
                    this.initialize()
                    this.error = 'Ошибка добавления'
                  })
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
      getCharacteristics (characteristics) {
        let stringCharacteristics = ''
        for (let i = 0; i < characteristics.length; i++) {
          stringCharacteristics += characteristics[i].text
          if (i < characteristics.length - 1) stringCharacteristics += ', '
        }
        return stringCharacteristics
      }
    }
  }
</script>

<style>

</style>