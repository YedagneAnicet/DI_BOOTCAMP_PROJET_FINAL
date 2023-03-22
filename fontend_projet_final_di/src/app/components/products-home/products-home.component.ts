import { Component, OnInit } from '@angular/core';
import { GestionPannierService } from 'src/app/services/gestion-pannier.service';
import { ProduitsService } from 'src/app/services/produits.service';

@Component({
  selector: 'app-products-home',
  templateUrl: './products-home.component.html',
  styleUrls: ['./products-home.component.scss']
})
export class ProductsHomeComponent implements OnInit{
  public productList: any;

  quantity: number = 1;

  constructor(
    private _produitService: ProduitsService,
    private _pannier: GestionPannierService
  ) {}

  ngOnInit(): void {
    this._produitService.getAllProducts().subscribe((response) => {
      response.sort(() => Math.random() - 0.5);
      const selectedProducts = response.slice(0, 6);
      selectedProducts.forEach((a: any) => {
        Object.assign(a, {
          quantity: this.quantity,
          total: a.prix_produit * this.quantity,
        });
      });
      this.productList = selectedProducts;
    });
  }


  addtocart(item: any) {
    this._pannier.addToCart(item);
  }
}
