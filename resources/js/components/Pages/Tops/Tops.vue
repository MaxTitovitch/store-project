<template>
  <v-container grid-list-md text-xs-center class="mt-5 mb-5">
    <v-layout row wrap>
      <v-flex xs12 mt-5>
        <h2 class="display-1 title-admin text-center text--secondary">Топы</h2>
      </v-flex>
      <v-flex xs12 mt-5 px-5 align-items-center>
        <v-card
            class="mx-auto"
        >
          <v-list subheader>
            <v-subheader>Топы</v-subheader>

            <v-list-item
                v-for="item in tops"
                :key="item.name"
                :to="'/tops/' + item.id"
                three-line
            >
              <v-list-item-avatar height="150px" width="150px" style="border-radius: 0!important;">
                <v-img :src="'/storage/posts/' + item.slug + '.png'"/>
              </v-list-item-avatar>

              <v-list-item-content>
                <strong><v-list-item-title v-text="item.name"/></strong>
                <v-list-item-content class="post-text" v-text="item.text"/>
              </v-list-item-content>

              <v-list-item-icon>
                <v-list-item-title
                    v-text="new Date(item.created_at).toISOString().substr(0, 10) + ' '
                      + new Date(item.created_at).toISOString().substr(11, 5)"
                />
              </v-list-item-icon>
            </v-list-item>
          </v-list>
        </v-card>

      </v-flex>

      <v-pagination
          v-model="page"
          :length="total"
          :total-visible="7"
          circle
          color="#FF9765"
          @input="runToPage"
      />
    </v-layout>
  </v-container>
</template>

<script>
  export default {
    data () {
      return {
        page: 1,
        total: 1,
        tops: [],
      }
    },
    created () {
      this.initialize();
    },
    methods: {
      initialize() {  //
        this.page = Number.parseInt(this.$route.query.page) || 1;
        let offset = (this.page - 1) * 10;

        this.$store.dispatch('getEntity', {entity: 'posts', data: {where: 'top_id is not null', count: 'true'}})
          .then((resp) => {
            this.total= Math.ceil(resp.data / 10);
          })
          .catch(err => this.$router.push('/'));
        this.$store.dispatch('getEntity', {entity: 'posts', data: {where: 'top_id is not null', offset, limit: 10, order: 'id desc'}})
          .then((resp) => {
            this.tops = resp.data
          })
          .catch(err => this.$router.push('/'));
      },
      runToPage(page) {
        this.$router.push('/tops?page=' + page);
        this.initialize();
      }
    }
  }
</script>

<style>
  .v-pagination--circle .v-pagination__item,
  .v-pagination--circle .v-pagination__more,
  .v-pagination--circle .v-pagination__navigation {
    border: 1px solid !important;
  }

  .post-text {
    overflow: hidden !important1;
    text-overflow: ellipsis;
    display: -webkit-box !important;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }
</style>
