import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class GestionPannierService {
  public cartItemList: any = [];
  public productList = new BehaviorSubject<any>([]);

  private cartItemsSubject: BehaviorSubject<any[]> = new BehaviorSubject<any>(
    []
  );
  public cartItems = this.cartItemsSubject.asObservable();

  constructor() {
    const cartItems = this.getCartItemsFromLocalStorage();
    this.cartItemList = cartItems;
    this.productList.next(this.cartItemList);
    this.cartItemsSubject.next(cartItems);
  }

  getProducts() {
    return this.productList.asObservable();
  }

  setProduct(product: any) {
    this.cartItemList.push(...product);
    this.productList.next(product);
    this.cartItemsSubject.next(this.cartItemList);
    this.saveCartItemsToLocalStorage(this.cartItemList);
  }

  addToCart(product: any) {
    // Chercher le produit dans le panier
    const existingProduct = this.cartItemList.find(
      (p: any) => p.id_produit === product.id_produit
    );
    if (existingProduct) {
      // Le produit existe déjà dans le panier, mettre à jour la quantité
      existingProduct.quantity += product.quantity;
    } else {
      // Ajouter le produit au panier dans le cas contraire
      this.cartItemList.push(product);
    }
    this.productList.next(this.cartItemList);
    this.cartItemsSubject.next(this.cartItemList);
    this.getTotalPrice();
    this.saveCartItemsToLocalStorage(this.cartItemList);
  }

  getTotalPrice(): number {
    let grandTotal = 0;
    this.cartItemList.forEach((product: any) => {
      grandTotal += (product.prix_produit * product.quantity);
    });
    return grandTotal;
  }

  removeCartItem(product: any) {
    this.cartItemList = this.cartItemList.filter(
      (a: any) => a.id_produit !== product.id_produit
    );
    this.productList.next(this.cartItemList);
    this.cartItemsSubject.next(this.cartItemList);
    this.saveCartItemsToLocalStorage(this.cartItemList);
  }

  removeAllCart() {
    this.cartItemList = [];
    this.productList.next(this.cartItemList);
    this.cartItemsSubject.next(this.cartItemList);
    this.saveCartItemsToLocalStorage(this.cartItemList);
  }

  private saveCartItemsToLocalStorage(cartItems: any[]) {
    localStorage.setItem('cartItems', JSON.stringify(cartItems));
  }

  private getCartItemsFromLocalStorage(): any[] {
    const cartItemsString = localStorage.getItem('cartItems');
    return cartItemsString ? JSON.parse(cartItemsString) : [];
  }
}
