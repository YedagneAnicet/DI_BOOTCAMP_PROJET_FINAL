import { GestionPannierService } from './../../services/gestion-pannier.service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-pannier',
  templateUrl: './pannier.component.html',
  styleUrls: ['./pannier.component.scss'],
})
export class PannierComponent {
  public products: any = [];
  public grandTotal!: number;

  constructor(private _pannier: GestionPannierService) {}
  ngOnInit(): void {
    this._pannier.getProducts().subscribe((respone) => {
      this.products = respone;
      this.grandTotal = this._pannier.getTotalPrice();
    });
  }

  removeItem(item: any) {
    this._pannier.removeCartItem(item);
  }

  emptycart() {
    this._pannier.removeAllCart();
  }

  increaseQuantity(item: any) {
    item.quantity++;
    this.grandTotal = this._pannier.getTotalPrice();
  }

  decreaseQuantity(item: any) {
    if (item.quantity > 1) {
      item.quantity--;
      this.grandTotal = this._pannier.getTotalPrice();
    }
  }
}
