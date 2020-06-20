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
      <template v-slot:item.characteristic_id="{ item }">
        {{ getTextByValue(item.characteristic_id, characteristicItems) }}
      </template>
      <template v-slot:item.boolean_value="{ item }">
        {{ item.boolean_value === 1 ? 'Да' : item.boolean_value === 0 ? 'Нет' : '-' }}
      </template>
      <template v-slot:item.number_value="{ item }">
        {{ item.number_value || '-' }}
      </template>
      <template v-slot:item.string_value="{ item }">
        {{ item.string_value || '-' }}
      </template>
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">CRUD опеарции: Характеристики {{ `(${productName})` }}</v-toolbar-title>
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

                      <v-col cols="12" sm="6" md="6">
                        <v-select
                            :items="characteristicItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.characteristic_id"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Характеристика"
                            color="#ff9966"
                            :rules="selectedRules"
                        />
                      </v-col>
                      <v-col cols="12" sm="6" md="6" v-if="getCharacteristic(editedItem.characteristic_id).type == 'boolean'">
                        <v-switch
                            :label="`Значение: ${editedItem.boolean_value ? 'Да' : 'Нет'}`"
                            v-model="editedItem.boolean_value"
                            color="#ff9966"
                        />
                      </v-col>

                      <v-col cols="12" sm="6" md="6" v-if="getCharacteristic(editedItem.characteristic_id).type == 'number'">
                        <v-text-field color="#FF9765" v-model="editedItem.number_value" :rules="numberRules" label="Значение"/>
                      </v-col>

                      <v-col cols="12" sm="6" md="6" v-if="getCharacteristic(editedItem.characteristic_id).type == 'string'">
                        <v-select
                            :items="getCharacteristic(editedItem.characteristic_id).values"
                            item-text="value"
                            item-value="value"
                            v-model="editedItem.string_value"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Значение"
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
        product: '',
        search: '',
        message: '',
        error: '',
        dialog: false,
        valid: false,
        selected: [],
        characteristicItems: [],
        valueItems: [],
        headers: [
          { text: 'ИД', value: 'id' },
          { text: 'Процент', value: 'characteristic_id' },
          { text: 'Значение булево', value: 'boolean_value' },
          { text: 'Значение числовое', value: 'number_value' },
          { text: 'Значение строковое', value: 'string_value' },
          { text: 'Действия', value: 'actions', sortable: false },
        ],
        items: [],
        editedIndex: -1,
        editedItem: {
          characteristic_id: '',
          boolean_value: '',
          number_value: '',
          string_value: '',
        },
        defaultItem: {
          characteristic_id: '',
          boolean_value: '',
          number_value: '',
          string_value: '',
        },

        numberRules: [
          v => !!v || 'Введите цену!',
          v => {
            let characteristic = this.getCharacteristic(this.editedItem.characteristic_id);
            let start = characteristic.int_value_start;
            let end = characteristic.int_value_end;
            return (v && !isNaN(v) && v > start && v <= end) || `Введите число от ${start} до ${end}!`;
          }
        ],
        selectedRules: [
          v => !!v || 'Выберите элемент!',
        ],
      }
    },

    created () {
      if (this.$route.query.id) {
        this.product = this.$route.query.id
        this.productName = this.$route.query.name
      } else {
        this.$router.push('/')
      }
      this.initialize()
    },
    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Создание характеристики товара' : 'Редактирование характеристики товара'
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
          data: { where: `product_id = ${this.product}`, order: 'id asc' },
          entity: 'product-characteristics'
        })
          .then((resp) => {
            this.items = resp.data
          })
          .catch(err => this.$router.push('/'))

        this.$store.dispatch('getOneEntity', { data: { id: 'id asc' }, id: this.product, entity: 'product-get-characteristics' })
          .then((resp) => {
            for (let i = 0; i < resp.data.length; i++) {
              this.characteristicItems.push({
                text: resp.data[i].name,
                value: resp.data[i].id,
                type:  resp.data[i].type,
                int_value_start:  resp.data[i].int_value_start,
                int_value_end:  resp.data[i].int_value_end,
                values: resp.data[i].characteristic_values
              })
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
        this.$store.dispatch('deleteEntity', { entity: 'product-characteristics', id: item.id })
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
        this.$router.push('/admin/products')
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
        let productCharacteristic = {
          characteristic_id: this.editedItem.characteristic_id,
          number_value: this.editedItem.number_value ,
          string_value: this.editedItem.string_value,
          product_id: this.product,
        }
        if(this.getCharacteristic(this.editedItem.characteristic_id).type === 'boolean') {
          productCharacteristic.boolean_value = this.editedItem.boolean_value ? 1 : 0;
        } else {
          productCharacteristic.boolean_value = '';
        }
        if(this.getCharacteristic(this.editedItem.characteristic_id).type === 'number') {
          productCharacteristic.number_value = Number.parseFloat(this.editedItem.number_value);
        } else {
          productCharacteristic.number_value = '';
        }
        if(this.getCharacteristic(this.editedItem.characteristic_id).type !== 'string') {
          productCharacteristic.string_value = '';
        }
        if (this.editedIndex > -1) {
          this.$store.dispatch('putEntity', { data: productCharacteristic, id: this.editedItem.id, entity: 'product-characteristics' })
            .then((resp) => {
              this.initialize()
            })
            .catch(err => {
                this.initialize()
                this.error = 'Ошибка обновления'
              }
            )
        } else {
          this.$store.dispatch('postEntity', { entity: 'product-characteristics', data: productCharacteristic })
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
      getCharacteristic (id) {
        for (let i = 0; i < this.characteristicItems.length; i++) {
          if(id === this.characteristicItems[i].value) {
            return this.characteristicItems[i];
          }
        }
        return  {type: false};
      }
    }
  }
</script>

<style>

</style>