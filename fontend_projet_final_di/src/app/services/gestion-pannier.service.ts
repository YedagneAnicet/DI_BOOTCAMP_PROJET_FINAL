import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class GestionPannierService {
   // Définition de variables
  public cartItemList: any = []; // tableau des produits dans le panier
  public productList = new BehaviorSubject<any>([]); // observable pour le tableau des produits

  // Définition d'un observable pour le tableau des produits du panier
  private cartItemsSubject: BehaviorSubject<any[]> = new BehaviorSubject<any>(
    []
  );
  public cartItems = this.cartItemsSubject.asObservable();

  constructor() {
    // Récupération des produits du panier depuis le stockage local et initialisation du tableau des produits dans le panier
    const cartItems = this.getCartItemsFromLocalStorage();
    this.cartItemList = cartItems;
    this.productList.next(this.cartItemList);
    this.cartItemsSubject.next(cartItems);
  }

   // Méthode pour obtenir l'observable pour le tableau des produit
  getProducts() {
    return this.productList.asObservable();
  }

   // Méthode pour ajouter un produit au panier
  setProduct(product: any) {
    this.cartItemList.push(...product);
    this.productList.next(product);
    this.cartItemsSubject.next(this.cartItemList);
    this.saveCartItemsToLocalStorage(this.cartItemList);
  }

  // Méthode pour ajouter un produit existant dans le panier
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

  // Méthode pour calculer le prix total du panier
  getTotalPrice(): number {
    let grandTotal = 0;
    this.cartItemList.forEach((product: any) => {
      grandTotal += (product.prix_produit * product.quantity);
    });
    return grandTotal;
  }

    // Méthode pour retirer un produit du panier
  removeCartItem(product: any) {
    this.cartItemList = this.cartItemList.filter(
      (a: any) => a.id_produit !== product.id_produit
    );
    this.productList.next(this.cartItemList);
    this.cartItemsSubject.next(this.cartItemList);
    this.saveCartItemsToLocalStorage(this.cartItemList);
  }

  // Méthode pour vide tout le pannier
  removeAllCart() {
    this.cartItemList = [];
    this.productList.next(this.cartItemList);
    this.cartItemsSubject.next(this.cartItemList);
    this.saveCartItemsToLocalStorage(this.cartItemList);
  }


  // Methode pour enregistrer le pannier dans le local Storage
  private saveCartItemsToLocalStorage(cartItems: any[]) {
    localStorage.setItem('cartItems', JSON.stringify(cartItems));
  }


  // Obtenir les elements du local Storage
  private getCartItemsFromLocalStorage(): any[] {
    const cartItemsString = localStorage.getItem('cartItems');
    return cartItemsString ? JSON.parse(cartItemsString) : [];
  }
}
