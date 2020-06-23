<template>
  <v-container grid-list-md text-xs-center class="mt-5">
    <v-layout row wrap>
      <v-flex xs12 mt-5>
        <h2 class="display-1 subheader-card title-admin">Статистика: ЗАКАЗЫ</h2>
      </v-flex>
      <v-flex xs12 mt-5 px-5 align-items-center>
        <v-card class="card-info card-product d-flex flex-wrap flex-column" color="#white" fill-height hover>
          <div class="my-5">
            <v-layout row wrap>
              <v-flex xs12 md8  class="flex align-center justify-center flex-wrap">
                <div>
                  <h2 class="display-1 title-admin" style="color: #FF9966">
                    {{ param === 'sex' ? 'Заказы по Полу' : (param === 'age' ? 'Заказы по Возрасту' : 'Заказы по Городу') }}
                  </h2>
                </div>
                <vc-donut
                    has-legend
                    legend-placement="bottom"
                    :sections="sections"
                    :thickness="Number.parseInt(thickness)"
                    :size="400"
                    :total="total || 100"
                >
                  <div style="color: #FF9966">
                    <div v-if="sections.length > 0">
                      <strong>{{ param === 'sex' ? 'Пол' : (param === 'age' ? 'Возраст' : 'Город') }}</strong>
                    </div>
                    <div v-if="sections.length <= 0">
                      Данные отсутствуют
                    </div>
                  </div>
                </vc-donut>

              </v-flex>
              <v-flex xs12 md4>
                <v-form v-model="valid" ref="form" validation>
                  <v-row>
                    <!--                    <v-col cols="10" >-->
                    <!--                      <v-menu-->
                    <!--                          ref="menu"-->
                    <!--                          v-model="menu"-->
                    <!--                          :close-on-content-click="false"-->
                    <!--                          transition="scale-transition"-->
                    <!--                          offset-y-->
                    <!--                          min-width="290px"-->
                    <!--                          color="#ff9966"-->
                    <!--                      >-->
                    <!--                        <template v-slot:activator="{ on }">-->
                    <!--                          <v-text-field-->
                    <!--                              v-model="dateRangeText"-->
                    <!--                              label="Данные по дате"-->
                    <!--                              readonly-->
                    <!--                              v-on="on"-->
                    <!--                              color="#ff9966"-->
                    <!--                              clearable-->
                    <!--                          />-->
                    <!--                        </template>-->
                    <!--                        <v-date-picker-->
                    <!--                            v-model="dateRange"-->
                    <!--                            scrollable-->
                    <!--                            locale="ru-ru"-->
                    <!--                            first-day-of-week="1"-->
                    <!--                            header-color="#ff9765"-->
                    <!--                            range-->
                    <!--                        >-->
                    <!--                          <v-spacer/>-->
                    <!--                          <v-btn text color="primary" @click="menu = false">Отмена</v-btn>-->
                    <!--                          <v-btn text color="primary" @click="$refs.menu.save(dateRange)">ОК</v-btn>-->
                    <!--                        </v-date-picker>-->
                    <!--                      </v-menu>-->
                    <!--                    </v-col>-->
                    <v-col cols="10">
                      <v-label>Статистика по параметру:</v-label>
                    </v-col>
                    <v-col cols="10">
                      <v-radio-group v-model="radios" :mandatory="false" @change="onSubmit">
                        <v-radio label="Пол" value="sex" color="#ff9966"/>
                        <v-radio label="Возраст" value="age" color="#ff9966"/>
                        <v-radio label="Город" value="city" color="#ff9966"/>
                      </v-radio-group>
                    </v-col>
                    <v-col cols="10">
                      <v-label>Толжина</v-label>
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
        sections: [],
        param: 'sex',
        total: 100,
        thickness: 20,
        valid: false,
        menu: false,
        dateRange: [],
        radios: [],
      }
    },
    computed: {
      dateRangeText () {
        return this.dateRange.join(' - ')
      },
    },
    created () {
      this.initialize()
    },
    methods: {
      initialize () {
        if (['sex', 'age', 'city'].lastIndexOf(this.$route.query.param) !== -1) {
          this.radios = this.param = this.$route.query.param
        } else {
          this.radios = this.param = 'sex'
        }
        this.$store.dispatch('getSchedulePie', { param: this.param })
          .then((resp) => {
            let sections = resp.data
            this.sections = this.parseData(sections, this.param)
          })
          .catch(err => this.$router.push('/'))
      },
      parseData (sections, param) {
        this.total = 0
        for (let i = 0; i < sections.length; i++) {
          sections[i] = {
            label: param === 'sex' ? sections[i].sex : (param === 'age' ? sections[i].year : sections[i].city),
            value: sections[i].count
          }
          this.total += sections[i].value
        }
        for (let i = 0; i < sections.length; i++) {
          sections[i].label += ` (${Math.round(sections[i].value / this.total * 10000) / 100}%) `
        }
        return sections
      },
      onSubmit () {
        this.$router.push(`/admin/schedule/pie?param=${this.radios}`)
        this.initialize()
      },
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