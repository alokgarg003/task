<?php

class ShoppingCart {
    private $products = ["Product A" => 20, "Product B" => 40, "Product C" => 50];
    private $cart = [];
    private $discountRules;

    public function __construct() {
        $this->discountRules = [
            "flat_10_discount" => function($totalQty, $_, $totalPrice) {
                return $totalPrice > 200 ? 10 : 0;
            },
            "bulk_5_discount" => function($qty, $price, $_) {
                return $qty > 10 ? 0.05 * $price : 0;
            },
            "bulk_10_discount" => function($totalQty, $totalPrice, $_) {
                return $totalQty > 20 ? 0.1 * $totalPrice : 0;
            },
            "tiered_50_discount" => function($totalQty, $qtyPerProduct, $price) {
                return $totalQty > 30 && $qtyPerProduct > 15 ? 0.5 * $price * ($qtyPerProduct - 15) : 0;
            }
        ];
    }

    private function calculateDiscount($totalQty, $qtyPerProduct, $totalPrice) {
        $discounts = [];
        foreach ($this->discountRules as $rule => $ruleFunc) {
            $discounts[$rule] = $ruleFunc($totalQty, $qtyPerProduct, $totalPrice);
        }
        $maxDiscountRule = array_keys($discounts, max($discounts))[0];
        return [$maxDiscountRule, max($discounts)];
    }

    private function calculateShippingFee($totalQty) {
        return floor($totalQty / 10) * 5;
    }

    private function calculateTotal($subTotal, $discountAmount, $shippingFee, $giftWrapFee) {
        return $subTotal - $discountAmount + $shippingFee + $giftWrapFee;
    }

    public function run() {
        foreach ($this->products as $product => $price) {
            $quantity = readline("Enter the quantity of $product: ");
            $isGiftWrapped = strtolower(readline("Is $product wrapped as a gift? (yes/no): ")) === "yes";
            $this->cart[$product] = ["quantity" => $quantity, "price" => $price, "giftWrap" => $isGiftWrapped];
        }

        $totalQty = array_sum(array_column($this->cart, "quantity"));
        $totalPrice = array_sum(array_map(function ($item) {
            return $item["quantity"] * $item["price"];
        }, $this->cart));

        $subTotal = array_sum(array_map(function ($item) {
            return $item["quantity"] * $item["price"];
        }, $this->cart));
        list($discountName, $discountAmount) = $this->calculateDiscount($totalQty, max(array_column($this->cart, "quantity")), $totalPrice);
        $shippingFee = $this->calculateShippingFee($totalQty);
        $giftWrapFee = array_sum(array_map(function ($item) {
            return $item["quantity"] * 1;
        }, array_filter($this->cart, function ($item) {
            return $item["giftWrap"];
        })));

        $total = $this->calculateTotal($subTotal, $discountAmount, $shippingFee, $giftWrapFee);

        foreach ($this->cart as $product => $item) {
    $quantity = $item['quantity'];
    $totalAmount = $quantity * $item['price'];
    echo "$product: Quantity: $quantity, Total Amount: $totalAmount\n";
}


        echo "\nSubtotal: $subTotal\n";
        echo "Discount Applied: $discountName, Amount: $discountAmount\n";
        echo "Shipping Fee: $shippingFee\n";
        echo "Gift Wrap Fee: $giftWrapFee\n";
        echo "Total: $total\n";
    }
}

$cart = new ShoppingCart();
$cart->run();

?>
