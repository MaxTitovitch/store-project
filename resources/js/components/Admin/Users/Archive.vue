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
      <template v-slot:item.email_verified_at="{ item }">
        <v-chip>{{ item.email_verified_at ? 'Да' : 'Нет' }}</v-chip>
      </template>
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">Архив: Пользователи</v-toolbar-title>
          <v-divider
              class="mx-4"
              inset
              vertical
          />
          <v-spacer/>
          <v-dialog max-width="500px">
            <template v-slot:activator="{ on, attrs }">

              <v-btn
                  color="#FF5F66"
                  dark
                  class="gradient-project mx-2"
                  :disabled="selected.length < 1"
                  @click="restoreItems()"
              >
                Востановить
              </v-btn>
              <v-btn
                  color="#FF5F66"
                  dark
                  class="gradient-project mx-2"
                  @click="showUsers()"
              >
                Назад к Пользователям
              </v-btn>
            </template>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon
            small
            @click="restoreItem(item)"
        >
          mdi-file-restore
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
    <div v-if="isLoggedRole !== 'Главный администратор'" class="lock"></div>
  </div>

</template>

<script>
  import { ImagePicker, imageUploadingStates } from '@nagoos/vue-image-picker'

  export default {
    components: {
      ImagePicker
    },
    data () {
      return {
        addImagesLabel: 'Добавить',
        message: '',
        error: '',
        search: '',
        selected: [],
        headers: [
          { text: 'ИД', value: 'id' },
          { text: 'Имя', value: 'name' },
          { text: 'Фамилия', value: 'last_name' },
          { text: 'Пол', value: 'sex' },
          { text: 'Дата рождения', value: 'date_of_birth' },
          { text: 'Почта', value: 'email' },
          { text: 'Верифицирован?', value: 'email_verified_at', sortable: false },
          { text: 'Телефон', value: 'phone' },
          { text: 'Роль', value: 'role' },
          { text: 'Действия', value: 'actions', sortable: false },
        ],
        items: [],
      }
    },
    created () {
      this.initialize()
    },
    computed: {
      isLoading: function () {return this.$store.getters.isLoading },
      isLoggedRole: function () { return this.$store.getters.isLoggedRole },
    },
    methods: {
      initialize () {
        this.$store.dispatch('getEntity', {data: { trash: 'true', order: 'id asc' }, entity: 'users'})
          .then((resp) => {
            this.items = resp.data
          })
          .catch(err => this.$router.push('/'))
      },

      restoreItem (item) {
        let user = {
          name: item.name,
          last_name: item.last_name,
          role: item.role,
          sex: item.sex,
          date_of_birth: item.date_of_birth,
          email: item.email,
          phone: item.phone,
          deleted_at: null,
        }
        this.$store.dispatch('putEntity', { entity: 'users', id: item.id, data: user })
          .then((resp) => {
            const index = this.items.indexOf(item)
            this.items.splice(index, 1);
          })
          .catch(err => {
              this.initialize()
              this.error = 'Ошибка удаления'
            }
          )
      },

      restoreItems () {
        for (let i = 0; i < this.selected.length; i++) {
          this.restoreItem(this.selected[i])
        }
      },
      showUsers () {
        this.$router.push('/admin/users')
      },
    }
  }
</script>

<style>
  .file-input {
    display: none !important;
  }

  .file-input-area i {
    display: none !important;
  }

  .file-input-area .layout {
    display: flex;
    justify-content: center;
  }

  .file-input-area .layout div {
    text-align: center;
  }

  .v-snack__content {
    background: linear-gradient(180deg, #FF5F66, #FF9765);
  }

  .v-snack {
    opacity: 0;
  }

  .lock {
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    background-image: url('/lock.jpg');
    background-position: center;
    background-size: 100% 100%;
  }
</style>