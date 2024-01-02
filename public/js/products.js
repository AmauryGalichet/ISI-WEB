
var price = 7;

$(".container .ip-add-cart").on("click", function() {
  var num = parseInt($(".container .qty .number").text()) || 1;
  updatePrice(num, price);
});

$(".container .fa-plus").on("click", function() {
  var num = parseInt($(".container .qty .number").text()) + 1 || 1;
  $(".container .qty .number").text(num);
  updatePrice(num, price);
});

$(".container .fa-minus").on("click", function() {
  var num = parseInt($(".container .qty .number").text()) - 1 || 1;
  if (num > 0)
    $(".container .qty .number").text(num);
  updatePrice(num, price);
});

function updatePrice(num, price) {
  $(".container .properties .price .amount").text(num * price + " EUR");
}
