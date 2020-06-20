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
          'items-per-page-options': [10, 20, 40, {text: 'Всё', value: -1}],
        }"
    >
      <template v-slot:item.int_value_start="{ item }">
        {{ item.int_value_start || '-' }}
      </template>
      <template v-slot:item.int_value_end="{ item }">
        {{ item.int_value_end || '-' }}
      </template>
      <template v-slot:item.values="{ item }">
        {{ item.values || '-' }}
      </template>
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">CRUD опеарции: Характеристики</v-toolbar-title>
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
                      <v-col cols="12" sm="6">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.name"
                            :rules="nameRules"
                            label="Название"/>
                      </v-col>

                      <v-col cols="12" sm="6">
                          <v-select
                              :items="typeItems"
                              item-text="text"
                              item-value="value"
                              v-model="editedItem.type"
                              item-color="#ff9966"
                              aria-required="true"
                              label="Тип"
                              color="#ff9966"
                              :rules="selectedRules"
                          />
                      </v-col>

                      <v-col cols="12" sm="6" v-if="editedItem.type === 'number'">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.int_value_start"
                            :rules="intStartRules"
                            @change="onChangeStart()"
                            label="Диапазон. Начало"/>
                      </v-col>

                      <v-col cols="12" sm="6" v-if="editedItem.type === 'number'">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.int_value_end"
                            :rules="intEndRules"
                            label="Диапазон. Конец"/>
                      </v-col>

                      <v-col cols="12" sm="12" md="12" v-if="editedItem.type === 'string'">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.values"
                            :rules="valueRules"
                            label="Значения (через запятую с пробелом)"/>
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
        isStartString: false,
        valid: false,
        selected: [],
        typeItems: [{text: 'Булев', value: 'boolean'}, {text: 'Числовой', value: 'number'}, {text: 'Строковый', value: 'string'}],
        headers: [
          { text: 'ИД', value: 'id' },
          { text: 'Название', value: 'name' },
          { text: 'Тип', value: 'type' },
          { text: 'Диапазон. Начало', value: 'int_value_start' },
          { text: 'Диапазон. Конец', value: 'int_value_end' },
          { text: 'Значения', value: 'values' },
          { text: 'Действия', value: 'actions', sortable: false },
        ],
        items: [],
        editedIndex: -1,
        editedItem: {
          name: '',
          type: '',
          int_value_start: '',
          int_value_end: '',
          values: '',
        },
        defaultItem: {
          name: '',
          type: '',
          int_value_start: '',
          int_value_end: '',
          values: '',
        },
        nameRules: [
          v => (v && v.length >= 3) || 'Текст должен быть длинной более 3-х символов'
        ],
        intStartRules: [
          v => !!v || 'Введите начало диапазона!',
          v => (v && !isNaN(v)) || 'Введите число!',
        ],
        intEndRules: [
          v => !!v || 'Введите начало диапазона!',
          v => (v && !isNaN(v) && v >= this.editedItem.int_value_start) || 'Введите число больше начала!',
        ],
        valueRules: [
          v => (v && v.length >= 3) || 'Значени должены быть длинной более 3-х символов'
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
        return this.editedIndex === -1 ? 'Создание характеристики' : 'Редактирование характеристики'
      },
      isLoading: function () { return this.$store.getters.isLoading }
    },
    watch: {
      dialog (val) {
        val || this.close()
      },
    },
    methods: {
      onChangeStart () {
        this.editedItem.int_value_end = null
      },

      initialize () {
        this.$store.dispatch('getEntity', {data: { order: 'id asc' }, entity: 'characteristics'})
          .then((resp) => {
            this.items = resp.data
            for (let i = 0; i < this.items.length; i++) {
              if(this.items[i].type === 'string') {
                    this.items[i].values = ''
                    for (let j = 0; j < this.items[i].characteristic_values.length; j++) {
                      this.items[i].values += this.items[i].characteristic_values[j].value;
                      if(j < this.items[i].characteristic_values.length-1) this.items[i].values += ', '
                    }

              } else {
                this.items[i].values = '';
              }
            }
          })
          .catch(err => this.$router.push('/'))
      },

      editItem (item) {
        this.editedIndex = this.items.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.isStartString = this.editedItem.type === 'string';
        this.dialog = true
      },

      createItem () {
        this.editedIndex = -1
        this.dialog = true
      },

      deleteItem (item) {
        this.$store.dispatch('deleteEntity', {entity: 'characteristics', id: item.id })
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
        this.$refs.form.reset();
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      save () {
        let characteristic = {
          name: this.editedItem.name,
          type: this.editedItem.type,
        }
        if(characteristic.type === 'number') {
          characteristic.int_value_start = this.editedItem.int_value_start;
          characteristic.int_value_end = this.editedItem.int_value_end;
        } else {
          characteristic.int_value_start = '';
          characteristic.int_value_end = '';
        }
        if (this.editedIndex > -1) {
          if (this.isStartString) {
            this.$store.dispatch('deleteEntity', {entity: 'characteristic-values-delete-by-characteristic', id: this.editedItem.id })
              .then((resp) => {})
              .catch(err => {})
          }
          let characteristicValues = this.editedItem.values.split(',') || this.editedItem.values;
          this.$store.dispatch('putEntity', { data: characteristic, id: this.editedItem.id, entity: 'characteristics'})
            .then((resp) => {
              let editedItem = resp.data;
              if(editedItem.type === 'string') {
                for (let i = 0; i < characteristicValues.length; i++) {
                  characteristicValues[i] = {value: characteristicValues[i], characteristic_id: resp.data.id};
                  this.$store.dispatch('postEntity', {entity: 'characteristic-values', data: characteristicValues[i] })
                    .then((resp) => {this.initialize()}).catch(err => {this.error = 'Ошибка добавления'})
                }
              }
              this.initialize()
            })
            .catch(err => {
                this.initialize()
                this.error = 'Ошибка обновления'
              }
            )
        } else {
          let characteristicValues = this.editedItem.values.split(',') || this.editedItem.values;
          this.$store.dispatch('postEntity', {entity: 'characteristics', data: characteristic})
            .then((resp) => {
              let editedItem = resp.data;
              if(editedItem.type === 'string') {
                for (let i = 0; i < characteristicValues.length; i++) {
                  characteristicValues[i] = {value: characteristicValues[i], characteristic_id: resp.data.id};
                  this.$store.dispatch('postEntity', {entity: 'characteristic-values', data: characteristicValues[i] })
                    .then((resp) => {this.initialize()}).catch(err => {this.error = 'Ошибка добавления'})
                }
              }
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

<style>

</style>