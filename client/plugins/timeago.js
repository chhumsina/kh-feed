import Vue from 'vue'
import VueTimeago from 'vue-timeago'
import toNow from 'date-fns/distance_in_words_to_now'

Vue.use(VueTimeago, {
  name: "Timeago",
  converter: (date, locale, converterOptions) => {
    const { includeSeconds, addSuffix = true } = converterOptions
    return toNow(date, {
      locale,
      includeSeconds,
      addSuffix
    }).replace("less than a minute ago", "just now") // here
  }
});
