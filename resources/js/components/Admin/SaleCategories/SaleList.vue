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
      <template v-slot:item.product_id="{ item }">
        {{ getTextByValue(item.product_id, productItems) }}
      </template>
      <template v-slot:item.sale_category_id="{ item }">
        {{ getTextByValue(item.sale_category_id, categoryItems) }}
      </template>
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">CRUD опеарции: Акции на товар</v-toolbar-title>
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
                  @click="goBack()"
              >
                Назад
              </v-btn>
              <v-btn
                  color="#FF5F66"
                  dark
                  class="gradient-project mx-2"
                  :disabled="selected.length < 1"
                  @click="deleteItems()"
              >
                Удалить
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
                  style="max-width: 10%"
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
                            v-model="editedItem.percent"
                            :rules="percentRules"
                            label="Процент"
                        />
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-menu
                            ref="menuStart"
                            v-model="menuStart"
                            :close-on-content-click="false"
                            transition="scale-transition"
                            offset-y
                            min-width="290px"
                            color="#ff9966"
                        >
                          <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="editedItem.date_start"
                                label="Дата начала"
                                readonly
                                v-on="on"
                                :rules="dateRules"
                                color="#ff9966"
                            />
                          </template>
                          <v-date-picker
                              v-model="editedItem.date_start"
                              scrollable
                              locale="ru-ru"
                              :min="'1900-01-01'"
                              :max="getNextYear()"
                              first-day-of-week="1"
                              header-color="#ff9765"
                          >
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="menuStart = false">Отмена</v-btn>
                            <v-btn text color="primary" @click="$refs.menuStart.save(editedItem.date_start)">ОК</v-btn>
                          </v-date-picker>
                        </v-menu>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-menu
                            ref="menuEnd"
                            v-model="menuEnd"
                            :close-on-content-click="false"
                            transition="scale-transition"
                            offset-y
                            min-width="290px"
                            color="#ff9966"
                        >
                          <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="editedItem.date_end"
                                label="Дата окончания"
                                readonly
                                v-on="on"
                                :rules="dateEndRules"
                                color="#ff9966"
                            />
                          </template>
                          <v-date-picker
                              v-model="editedItem.date_end"
                              scrollable
                              locale="ru-ru"
                              :min="editedItem.date_start"
                              :max="getNextYear()"
                              first-day-of-week="1"
                              header-color="#ff9765"
                          >
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="menuEnd = false">Отмена</v-btn>
                            <v-btn text color="primary" @click="$refs.menuEnd.save(editedItem.date_end)">ОК</v-btn>
                          </v-date-picker>
                        </v-menu>
                      </v-col>

                      <v-col cols="12" sm="6" md="4">
                        <v-select
                            :items="productItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.product_id"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Товар"
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
        saleCategory: '',
        search: '',
        message: '',
        error: '',
        menuStart: '',
        menuEnd: '',
        dialog: false,
        valid: false,
        selected: [],
        productItems: [],
        categoryItems: [],
        headers: [
          { text: 'ИД', value: 'id' },
          { text: 'Процент', value: 'percent' },
          { text: 'Дата начала', value: 'date_start' },
          { text: 'Дата окончания', value: 'date_end' },
          { text: 'Товар', value: 'product_id' },
          { text: 'Категория', value: 'sale_category_id' },
          { text: 'Действия', value: 'actions', sortable: false },
        ],
        items: [],
        editedIndex: -1,
        editedItem: {
          percent: '',
          date_start: '',
          date_end: '',
          product_id: '',
        },
        defaultItem: {
          percent: '',
          date_start: '',
          date_end: '',
        },
        percentRules: [
          v => !!v || 'Введите процент!',
          v => (v && !isNaN(v) && v > 0 && v < 100) || 'Введите число от 1 до 99!',
        ],
        dateRules: [
          v => !!v || 'Введите дату!'
        ],
        dateEndRules: [
          v => !!v || 'Введите дату!',
          v => new Date(v) > new Date(this.editedItem.date_start) || 'Введите после начала!'
        ],
        selectedRules: [
          v => !!v || 'Выберите элемент!',
        ],
      }
    },

    created () {
      if (this.$route.query.id) {
        this.saleCategory = this.$route.query.id
      } else {
        this.$router.push('/')
      }
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
        this.$store.dispatch('getEntity', {
          data: { where: `sale_category_id = ${this.saleCategory}`, order: 'id asc' },
          entity: 'sales'
        })
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
          .catch(err => { this.$router.push('/')})

        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'sale-categories' })
          .then((resp) => {
            for (let i = 0; i < resp.data.length; i++) {
              this.categoryItems.push({ text: resp.data[i].name, value: resp.data[i].id })
            }
          })
          .catch(err => { this.$router.push('/')})
      },

      showItem (id) {
        this.$router.push('/sales/' + id)
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
        this.$store.dispatch('deleteEntity', { entity: 'sales', id: item.id })
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

      goBack () {
        this.$router.push('/admin/sale-categories')
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
        let sale = {
          name: this.editedItem.percent,
          date_start: this.editedItem.date_start,
          date_end: this.editedItem.date_end,
          percent: this.editedItem.percent,
          product_id: this.editedItem.product_id,
          sale_category_id: this.saleCategory,
        }
        if (this.editedIndex > -1) {
          this.$store.dispatch('putEntity', { data: sale, id: this.editedItem.id, entity: 'sales' })
            .then((resp) => {
              this.initialize()
            })
            .catch(err => {
                this.initialize()
                this.error = 'Ошибка обновления'
              }
            )
        } else {
          this.$store.dispatch('postEntity', { entity: 'sales', data: sale })
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
      getNextYear () {
        let date = new Date()
        date.setFullYear(date.getFullYear() + 1)
        return date.toISOString().substr(0, 10)
      }
    }
  }
</script>

<style>

</style>