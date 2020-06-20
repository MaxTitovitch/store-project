<template>
  <v-container grid-list-md text-xs-center class="mt-5">
    <v-layout row wrap>
      <v-flex xs12 mt-5>
        <h2 class="display-1 subheader-card title-admin">
          Сущность: {{ entity.text }}.
          <a :href="`/products/${this.product.id}`" style="color: #ff9966">{{ this.product.name  + ' ' + (this.product.last_name || '')}}</a>
        </h2>
      </v-flex>
      <v-flex xs12 mt-5 px-5 align-items-center>
        <v-card class="card-info card-product d-flex flex-wrap flex-column" color="#white" fill-height hover>
          <div class="my-5">
            <v-layout row wrap>
              <v-flex xs12 md8>
                <div>
                  <h2 class="display-1 subheader-card title-admin" style="color: #FF9966">
                    {{ `Статистика по показателю: "${param.text + (entity.value === 'sale-category' ? ' на акции' :
                    '')}"`}}
                  </h2>
                </div>
                <div v-if="sections[0].data.length > 0">
                  <line-chart
                      :data="sections"
                      x-name="Название"
                      y-name=""
                      smooth
                      style="min-height: 400px"
                      :area="thickness"
                  />
                </div>
                <div v-if="sections[0].data.length <= 0">
                  <div style="color: #FF9966">
                    <h2 class="subheader-card title-admin" style="color: #FF9966">
                      Данные отсутствуют
                    </h2>
                  </div>
                </div>
              </v-flex>
              <v-flex xs12 md4>
                <v-form v-model="valid" ref="form" validation>
                  <v-row>
                    <v-col cols="10">
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
                              v-model="dateRangeText"
                              label="Данные по дате"
                              readonly
                              v-on="on"
                              color="#ff9966"
                              clearable
                              @click:clear="onSubmit(true)"
                              :disabled="entity.value === 'sale-category'"
                          />
                        </template>
                        <v-date-picker
                            v-model="dateRange"
                            scrollable
                            locale="ru-ru"
                            first-day-of-week="1"
                            header-color="#ff9765"
                            range
                        >
                          <v-spacer/>
                          <v-btn text color="primary" @click="() => {this.dateRangeText = ''; this.menu = false;}">
                            Отмена
                          </v-btn>
                          <v-btn text color="primary" @click="() => {$refs.menu.save(dateRange); onSubmit();}">ОК
                          </v-btn>
                        </v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="10">
                      <v-label>Статистика по параметру:</v-label>
                    </v-col>
                    <v-col cols="10">
                      <v-radio-group v-model="radios" :mandatory="false" @change="onSubmit">
                        <v-radio
                            v-for="(item, id) of entity.params"
                            :key="item"
                            :label="entity.paramsNames[id]"
                            :value="item"
                            color="#ff9966"/>
                      </v-radio-group>
                    </v-col>
                    <v-col cols="10">
                      <v-label>Толщина</v-label>
                    </v-col>
                    <v-col cols="10">
                      <v-switch
                          v-model="thickness"
                          :label="`Площадь: ${thickness ? 'Да' : 'Нет'}`"
                          color="#ff9966"
                      />
                    </v-col>
                  </v-row>
                </v-form>
              </v-flex>
            </v-layout>
          </div>
        </v-card>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  export default {
    data () {
      return {
        id: 0,
        sections: [{ name: 'Статистика', data: [] }],
        param: 'views',
        entity: {},
        product: {},
        valid: false,
        name: '',
        menu: false,
        thickness: false,
        dateRange: [],
        radios: [],
      }
    },
    computed: {
      dateRangeText: {
        get () {
          return this.dateRange.join(' ')
        },
        set (newValue) {
        }
      },
    },
    created () {
      this.initialize()
    },
    methods: {
      initialize () {
        switch (this.$route.query.entity) {
          default:
          case 'product':
            this.entity = {
              text: 'Товары', value: 'product', params: ['views', 'orders', 'rankings'],
              paramsNames: ['Просмотры', 'Заказы', 'Рейтинг']
            }
            break
          case 'post':
            this.entity = {
              text: 'Статьи', value: 'post', params: ['views', 'likes'],
              paramsNames: ['Просмотры', 'Лайки']
            }
            break
          case 'user':
            this.entity = {
              text: 'Пользователи', value: 'user', params: ['orders'],
              paramsNames: ['Заказы']
            }
            break
          case 'sale-category':
            this.entity = {
              text: 'Акции', value: 'sale-category', params: ['orders'],
              paramsNames: ['Заказы']
            }
            break
        }

        this.name = this.entity.value === 'sale-category' ? 'sale-categories' : this.entity.value + 's';
        this.$store.dispatch('getOneEntity', { data: { id: 'id asc' }, id: this.$route.query.id, entity: this.name })
          .then((resp) => {
            this.product = resp.data
          })
          .catch(err => { this.$router.push('/')})

        let id = 0
        if ((id = this.entity.params.lastIndexOf(this.$route.query.param)) !== -1) {
          this.param = { text: this.entity.paramsNames[id], value: this.entity.params[id] }
        } else {
          this.param = { text: this.entity.paramsNames[0], value: this.entity.params[0] }
        }
        this.dateRange = [this.$route.query.date_start, this.$route.query.date_end]
        this.radios = this.param.value
        let data = {
          date_start: this.$route.query.date_start || this.getLastYear(),
          date_end: this.$route.query.date_end || new Date().toISOString().substr(0, 10),
        }

        this.$store.dispatch('getScheduleLine', { entity: this.entity.value, param: this.param.value, id: this.$route.query.id, data })
          .then((resp) => {
            let sections = resp.data.dates
            this.sections[0].data = this.parseData(sections)
            this.sections[0].name = this.param.text
          })
          .catch(err => this.$router.push('/'))
      },
      parseData (sections) {
        let data = [];
        for (let value in sections) {
          data.push({
            label: value,
            value: sections[value]
          });
        }
        return data
      },
      onSubmit (isWithout = false) {
        let dataValues = ''
        if (this.dateRange.length > 0 && !isWithout && this.entity.value !== 'sale-category') {
          let dateOne = this.dateRange[0]
          let dateTwo = this.dateRange[this.dateRange.length === 1 ? 0 : 1]
          let isFirstOne = new Date(dateOne) > new Date(dateTwo)
          dataValues = `&date_start=${isFirstOne ? dateTwo : dateOne}&date_end=${isFirstOne ? dateOne : dateTwo}`
        }
        this.$router.push(`/admin/schedule/line?entity=${this.entity.value}&param=${this.radios}&id=${this.product.id}` + dataValues).catch(() => {})
        this.initialize()
      },
      getLastYear () {
        let date = new Date()
        date.setFullYear(date.getFullYear() - 1)
        return date.toISOString().substr(0, 10)
      }
    }
  }
</script>

<style scoped>


  .card-info > div {
    width: 100%;
  }

  .text-gray-footer a {
    color: #727271 !important;
  }

  .subheader-card {
    color: #646464;
    width: 100%;
    font-weight: 500 !important;
  }

  .card-product {
    border: 1px solid #646464 !important;
  }

  .title-admin {
    text-align: center;
  }

</style>