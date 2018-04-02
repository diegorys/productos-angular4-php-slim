import { Angular4proyectoPage } from './app.po';

describe('angular4proyecto App', () => {
  let page: Angular4proyectoPage;

  beforeEach(() => {
    page = new Angular4proyectoPage();
  });

  it('should display message saying app works', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('app works!');
  });
});
