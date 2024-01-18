class ShoppingCart:
    def __init__(self):
        self.products = {"Product A": 20, "Product B": 40, "Product C": 50}
        self.cart = {}
        self.discount_rules = {
            "flat_10_discount": lambda total_qty, _, total_price: 10 if total_price > 200 else 0,
            "bulk_5_discount": lambda qty, price, _: 0.05 * price if qty > 10 else 0,
            "bulk_10_discount": lambda total_qty, total_price, _: 0.1 * total_price if total_qty > 20 else 0,
            "tiered_50_discount": lambda total_qty, qty_per_product, price: 0.5 * price * (qty_per_product - 15)
            if total_qty > 30 and qty_per_product > 15
            else 0,
        }
        self.gift_wrap_fee = 1
        self.shipping_fee = 5

    def calculate_discount(self, total_qty, qty_per_product, total_price):
        discounts = {
            rule: rule_func(total_qty, qty_per_product, total_price)
            for rule, rule_func in self.discount_rules.items()
        }
        return max(discounts, key=discounts.get), max(discounts.values())

    def calculate_shipping_fee(self, total_qty):
        return (total_qty // 10) * self.shipping_fee

    def calculate_total(self, sub_total, discount_amount, shipping_fee, gift_wrap_fee):
        return sub_total - discount_amount + shipping_fee + gift_wrap_fee

    def run(self):
        for product, price in self.products.items():
            quantity = int(input(f"Enter the quantity of {product}: "))
            is_gift_wrapped = input(f"Is {product} wrapped as a gift? (yes/no): ").lower() == "yes"
            self.cart[product] = {"quantity": quantity, "price": price, "gift_wrap": is_gift_wrapped}

        total_qty = sum(item["quantity"] for item in self.cart.values())
        total_price = sum(item["quantity"] * item["price"] for item in self.cart.values())

        sub_total = sum(item["quantity"] * item["price"] for item in self.cart.values())
        discount_name, discount_amount = self.calculate_discount(total_qty, max(item["quantity"] for item in self.cart.values()), total_price)
        shipping_fee = self.calculate_shipping_fee(total_qty)
        gift_wrap_fee = sum(item["quantity"] * self.gift_wrap_fee for item in self.cart.values() if item["gift_wrap"])

        total = self.calculate_total(sub_total, discount_amount, shipping_fee, gift_wrap_fee)

        for product, item in self.cart.items():
            print(f"{product}: Quantity: {item['quantity']}, Total Amount: {item['quantity'] * item['price']}")

        print(f"\nSubtotal: {sub_total}")
        print(f"Discount Applied: {discount_name}, Amount: {discount_amount}")
        print(f"Shipping Fee: {shipping_fee}")
        print(f"Gift Wrap Fee: {gift_wrap_fee}")
        print(f"Total: {total}")


if __name__ == "__main__":
    cart = ShoppingCart()
    cart.run()
