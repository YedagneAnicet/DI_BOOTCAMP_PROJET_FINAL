import { Component } from '@angular/core';
import { ProduitsService } from 'src/app/services/produits.service';

@Component({
  selector: 'app-products',
  templateUrl: './products.component.html',
  styleUrls: ['./products.component.scss'],
})
export class ProductsComponent {
  public productList: any;

  constructor(private _produitService : ProduitsService) {}

  ngOnInit(): void {
    this._produitService.getAllProducts().subscribe((response)=> {
      this.productList = response;
      console.log(response)
    })
  }
}
