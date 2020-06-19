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
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">CRUD опеарции: Акции</v-toolbar-title>
          <v-divider
              class="mx-4"
              inset
              vertical
          />
          <v-spacer/>
          <v-dialog v-model="dialog" max-width="1000px">
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
              >
                Добавить
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
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.name"
                            :rules="nameRules"
                            label="Название"
                        />
                      </v-col>
                      <v-col cols="12" sm="12" md="12">
                        <v-textarea
                            color="#FF9765"
                            v-model="editedItem.description"
                            :rules="descRules"
                            label="Описание"
                            filled
                            auto-grow
                        />
                      </v-col>
                      <v-col cols="12" sm="12" md="12">
                        <v-btn
                            dark class="gradient-project" style="width: 100%" text
                            @click="showSales(editedItem.id)"
                            :disabled="editedIndex == -1"
                        >
                          Управление
                        </v-btn>
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
            @click="showSales(item.id)"
        >
          mdi-format-list-bulleted-square
        </v-icon>
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
      message: '',
      error: '',
      dialog: false,
      valid: false,
      selected: [],
      headers: [
        { text: 'ИД', value: 'id' },
        { text: 'Название', value: 'name' },
        { text: 'Описание', value: 'description' },
        { text: 'Действия', value: 'actions', sortable: false },
      ],
      items: [],
      editedIndex: -1,
      editedItem: {
        name: '',
        description: '',
      },
      defaultItem: {
        name: '',
        description: '',
      },
      nameRules: [
        v => (v && v.length >= 3) || 'Название должно быть длинной более 3-х символов'
      ],
      descRules: [
        v => (v && v.length >= 10) || 'Описание должно быть длинной более 10-х символов'
      ],
    }
  },

  created () {
    this.initialize()
  },
  computed: {
    formTitle () {
      return this.editedIndex === -1 ? 'Создание акции' : 'Редактирование акции'
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
      this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'sale-categories' })
        .then((resp) => {
          this.items = resp.data
        })
        .catch(err => this.$router.push('/'))
    },

    showItem (id) {
      this.$router.push('/sales/' + id)
    },

    showSales (id) {
      this.$router.push('/admin/sales?id=' + id)
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
      this.$store.dispatch('deleteEntity', { entity: 'sale-categories', id: item.id })
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
      this.$refs.form.reset()
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    save () {
      let saleCategory = {
        name: this.editedItem.name,
        description: this.editedItem.description,
      }
      if (this.editedIndex > -1) {
        this.$store.dispatch('putEntity', { data: saleCategory, id: this.editedItem.id, entity: 'sale-categories' })
          .then((resp) => {
            this.initialize()
          })
          .catch(err => {
              this.initialize()
              this.error = 'Ошибка обновления'
            }
          )
      } else {
        this.$store.dispatch('postEntity', { entity: 'sale-categories', data: saleCategory })
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