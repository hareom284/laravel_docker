export const paginateArray = (array, perPage, page) => array.slice((page - 1) * perPage, page * perPage)
export const genId = array => {
  const { length } = array
  let lastIndex = 0
  if (length)
    lastIndex = Number(array[length - 1]?.id) + 1
  
  return lastIndex || (length + 1)
}
