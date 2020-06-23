export default {
  state: {
    items: [],
    checkoutStatus: null
  },
  mutations: {
    pushProductToCart (state, product) {
      const cartItem = state.items.find(item => item.product.id === product.id)
      if(!cartItem) {
        state.items.push({ product: product, quantity: 1 })
      } else {
        cartItem.quantity++;
      }
    },

    incrementItemQuantity (state, { id }) {
      const cartItem = state.items.find(product => product.id === id)
      cartItem.quantity++
    },

    decrementItemQuantity (state, { id }) {
      const cartItem = state.items.find(product => product.id === id)
      cartItem.quantity--;
      if(cartItem.quantity <= 0) {
        state.items = state.items.filter(function( item ) {
          return item.field > 0;
        });
      }
    },

    setCartItems (state, { items }) {
      state.items = items
    },

    setCheckoutStatus (state, status) {
      state.checkoutStatus = status
    },

    clearCart (state, data) {
      state.items = [];
      state.checkoutStatus = null;
    },
  },
  actions: {
    pushProductToCart (context, payload) {
      context.commit('pushProductToCart', payload);
    },
    incrementItemQuantity (context, payload) {
      context.commit('incrementItemQuantity', payload);
    },
    decrementItemQuantity (context, payload) {
      context.commit('decrementItemQuantity', payload);
    },
    setCartItems (context, payload) {
      context.commit('setCartItems', payload);
    },
    setCheckoutStatus (context, payload) {
      context.commit('setCheckoutStatus', payload);
    },
    clearCart (context, payload) {
      context.commit('clearCart', payload);
    },
  },
  getters: {
    cartProducts: (state) => {
      return state.items;
    },

    isHave: (state) => {
      return state.items.length > 0 ? state.items.length : false;
    },
  }
}