import * as Cookies from 'js-cookie'
export default function ({ $auth }) {
  // Hack it anyway
  $auth.strategy.options.tokenName = 'X-XSRF-TOKEN'
  $auth.getToken = (name) => {
    return Cookies.get('XSRF-TOKEN') || ''
  }
  // if (!$auth.loggedIn) {
  //   return
  // }
  // // DO something
}
