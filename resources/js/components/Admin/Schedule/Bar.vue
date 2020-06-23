<template>
  <v-container grid-list-md text-xs-center class="mt-5">
    <v-layout row wrap>
      <v-flex xs12 mt-5>
        <h2 class="display-1 subheader-card title-admin">Сущность: {{ entity.text }}</h2>
      </v-flex>
      <v-flex xs12 mt-5 px-5 align-items-center>
        <v-card class="card-info card-product d-flex flex-wrap flex-column" color="#white" fill-height hover>
          <div class="my-5">
            <v-layout row wrap>
              <v-flex xs12 md8>
                <div>
                  <h2 class="display-1 subheader-card title-admin" style="color: #FF9966">
                    {{ `Статистика по показателю: "${param.text + (entity.value === 'sale-category' ? ' на акции' : '')}"`}}
                  </h2>
                </div>
                <div v-if="sections[0].data.length > 0" class="flex align-center justify-center flex-wrap" style="display: flex">
                  <BarChart
                      :data="sections"
                      x-name="Название"
                      y-name=""
                      zoom
                      rainbow
                      style="min-height: 400px"
                      :bar-max-width="`${thickness}%`"
                  />
                  <v-btn
                      color="#FF5F66"
                      dark
                      class="gradient-project mx-2 mt-5"
                      @click="downloadChart"
                  >
                    Сохранить график
                  </v-btn>
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
                          <v-btn text color="primary" @click="() => {this.dateRangeText = ''; this.menu = false;}">Отмена</v-btn>
                          <v-btn text color="primary" @click="() => {$refs.menu.save(dateRange); onSubmit();}">ОК</v-btn>
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
                      <v-select
                          :items="['3', '4', '5', '6', '7', '8', '9', '10']"
                          v-model="quantity"
                          item-color="#ff9966"
                          aria-required="true"
                          label="Количество"
                          color="#ff9966"
                          clearable
                          @change="onSubmit"
                      />
                    </v-col>
                    <v-col cols="10">
                      <v-label>Толщина</v-label>
                    </v-col>
                    <v-col cols="10">
                      <input type="range" style="width: 100%" min="1" max="100" step="1" v-model="thickness">
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
        sections: [{name: 'Статистика', data: []}],
        param: 'views',
        entity: {},
        valid: false,
        menu: false,
        quantity: 5,
        thickness: 50,
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
          default: case 'product':
            this.entity = {text: 'Товары', value: 'product', params: ['views', 'orders', 'rankings'],
              paramsNames: ['Просмотры', 'Заказы', 'Рейтинг']}; break;
          case 'post':
            this.entity = {text: 'Статьи', value: 'post', params: ['views', 'likes'],
              paramsNames: ['Просмотры', 'Лайки']}; break;
          case 'user':
            this.entity = {text: 'Пользователи', value: 'user', params: ['orders'],
              paramsNames: ['Заказы']}; break;
          case 'sale-category':
            this.entity = {text: 'Акции', value: 'sale-category', params: ['orders'],
              paramsNames: ['Заказы']}; break;
        }
        let id = 0;
        if((id = this.entity.params.lastIndexOf(this.$route.query.param)) !== -1) {
          this.param = {text: this.entity.paramsNames[id], value: this.entity.params[id]};
        } else {
          this.param = {text: this.entity.paramsNames[0], value: this.entity.params[0]};
        }
        this.dateRange = [this.$route.query.date_start, this.$route.query.date_end];
        this.radios = this.param.value;
        this.quantity = this.$route.query.quantity || 5;
        let data = {
          date_start: this.$route.query.date_start || '1900-01-01',
          date_end: this.$route.query.date_end || '2100-01-01',
          quantity: this.quantity
        }

        this.$store.dispatch('getScheduleBar', { entity: this.entity.value, param: this.param.value, data })
          .then((resp) => {
            let sections = resp.data
            this.sections[0].data = this.parseData(sections)
            this.sections[0].name = this.param.text
          })
          .catch(err => this.$router.push('/'))
      },
      parseData (sections) {
        for (let i = 0; i < sections.length; i++) {
          sections[i] = {
            label: sections[i].name || sections[i].last_name,
            value: sections[i].count
          }
        }
        return sections
      },
      onSubmit (isWithout = false) {
        let dataValues = '';
        if (this.dateRange.length > 0 && !isWithout && this.entity.value !== 'sale-category') {
          let dateOne = this.dateRange[0];
          let dateTwo = this.dateRange[this.dateRange.length === 1 ? 0 : 1];
          let isFirstOne = new Date(dateOne) > new Date(dateTwo);
          dataValues = `&date_start=${isFirstOne ? dateTwo : dateOne}&date_end=${isFirstOne ? dateOne : dateTwo}`
        }
        this.$router.push(`/admin/schedule/bar?entity=${this.entity.value}&param=${this.radios}&quantity=${this.quantity}` + dataValues).catch(()=>{});
        this.initialize()
      },
      downloadChart () {
        var canvas = document.querySelectorAll("canvas")[0];
        canvas.toBlob(function(blob) {
          saveAs(blob, "Диаграмма - На настолке.png");
        });
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